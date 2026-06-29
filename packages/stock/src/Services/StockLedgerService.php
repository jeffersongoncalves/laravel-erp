<?php

namespace JeffersonGoncalves\Erp\Stock\Services;

use DomainException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use JeffersonGoncalves\Erp\Accounting\Services\GeneralLedgerService;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Stock\Contracts\PostsStockLedger;
use JeffersonGoncalves\Erp\Stock\Enums\ValuationMethod;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver;

/**
 * The perpetual inventory engine.
 *
 * For every stock movement it writes an immutable {@see StockLedgerEntry} per
 * (item, warehouse), keeps the live {@see Bin} balance in step and posts the
 * net change in stock value to the general ledger. Two valuation methods are
 * supported: Moving Average (a single running weighted-average rate per bin)
 * and FIFO (an ordered queue of (qty, rate) layers persisted on the bin).
 */
class StockLedgerService
{
    /** Tolerance below which a quantity or value is treated as zero. */
    private const EPSILON = 0.000000001;

    public function __construct(
        private readonly GeneralLedgerService $generalLedger
    ) {}

    /**
     * Post stock-ledger entries for a voucher and the resulting GL impact.
     *
     * @param  SubmittableDocument&Model  $voucher
     * @param  list<array{item_id: int, warehouse_id: int, actual_qty: float|int, incoming_rate?: float|int, posting_date?: mixed}>  $movements
     *
     * @throws DomainException when an outbound movement would overdraw a bin
     *                         and negative stock is not allowed.
     */
    public function post(SubmittableDocument $voucher, array $movements): void
    {
        $totalDifference = 0.0;

        foreach ($movements as $movement) {
            $itemId = (int) $movement['item_id'];
            $warehouseId = (int) $movement['warehouse_id'];
            $actualQty = (float) $movement['actual_qty'];
            $incomingRate = (float) ($movement['incoming_rate'] ?? 0);
            $postingDate = $movement['posting_date'] ?? $voucher->getAttribute('posting_date');

            $valuationMethod = $this->valuationMethodForItem($itemId);
            $bin = $this->bin($itemId, $warehouseId);

            $state = [
                'qty' => (float) $bin->getAttribute('actual_qty'),
                'value' => (float) $bin->getAttribute('stock_value'),
                'rate' => (float) $bin->getAttribute('valuation_rate'),
                'queue' => $this->queueFromBin($bin),
            ];

            $result = $this->applyMovement($valuationMethod, $state, $actualQty, $incomingRate, true);

            $stockLedgerEntry = ModelResolver::stockLedgerEntry();
            $stockLedgerEntry::query()->create([
                'item_id' => $itemId,
                'warehouse_id' => $warehouseId,
                'posting_date' => $postingDate,
                'actual_qty' => $actualQty,
                'qty_after_transaction' => $result['qty_after_transaction'],
                'incoming_rate' => $result['incoming_rate'],
                'valuation_rate' => $result['valuation_rate'],
                'stock_value' => $result['stock_value'],
                'stock_value_difference' => $result['stock_value_difference'],
                'voucherable_type' => $voucher->getMorphClass(),
                'voucherable_id' => $voucher->getKey(),
                'company_id' => $voucher->getAttribute('company_id'),
                'is_cancelled' => false,
            ]);

            $this->writeBin($bin, $result['state']);

            $totalDifference += $result['stock_value_difference'];
        }

        $this->postGeneralLedger($voucher, $totalDifference);
    }

    /**
     * Reverse every active stock-ledger entry of a voucher.
     *
     * Originals are flagged cancelled and mirror rows (with the quantity and
     * value impact negated) are written for the audit trail; every affected bin
     * is then recomputed from the surviving entries and the GL is reversed.
     *
     * @param  SubmittableDocument&Model  $voucher
     */
    public function reverse(SubmittableDocument $voucher): void
    {
        $stockLedgerEntry = ModelResolver::stockLedgerEntry();

        /** @var Collection<int, Model> $entries */
        $entries = $stockLedgerEntry::query()
            ->where('voucherable_type', $voucher->getMorphClass())
            ->where('voucherable_id', $voucher->getKey())
            ->where('is_cancelled', false)
            ->get();

        /** @var array<string, array{int, int}> $affected */
        $affected = [];

        foreach ($entries as $entry) {
            $itemId = (int) $entry->getAttribute('item_id');
            $warehouseId = (int) $entry->getAttribute('warehouse_id');

            $stockLedgerEntry::query()->create([
                'item_id' => $itemId,
                'warehouse_id' => $warehouseId,
                'posting_date' => $entry->getAttribute('posting_date'),
                'actual_qty' => -1 * (float) $entry->getAttribute('actual_qty'),
                'qty_after_transaction' => 0,
                'incoming_rate' => $entry->getAttribute('incoming_rate'),
                'valuation_rate' => $entry->getAttribute('valuation_rate'),
                'stock_value' => 0,
                'stock_value_difference' => -1 * (float) $entry->getAttribute('stock_value_difference'),
                'voucherable_type' => $entry->getAttribute('voucherable_type'),
                'voucherable_id' => $entry->getAttribute('voucherable_id'),
                'company_id' => $entry->getAttribute('company_id'),
                'is_cancelled' => true,
            ]);

            $entry->setAttribute('is_cancelled', true);
            $entry->save();

            $affected[$itemId.'-'.$warehouseId] = [$itemId, $warehouseId];
        }

        foreach ($affected as [$itemId, $warehouseId]) {
            $this->recomputeBin($itemId, $warehouseId);
        }

        $this->generalLedger->reverse($voucher);
    }

    /**
     * Apply a single movement to a running bin state and derive the values for
     * its stock-ledger entry.
     *
     * @param  array{qty: float, value: float, rate: float, queue: list<array{qty: float, rate: float}>}  $state
     * @return array{
     *     state: array{qty: float, value: float, rate: float, queue: list<array{qty: float, rate: float}>},
     *     qty_after_transaction: float,
     *     incoming_rate: float,
     *     valuation_rate: float,
     *     stock_value: float,
     *     stock_value_difference: float
     * }
     */
    private function applyMovement(ValuationMethod $method, array $state, float $actualQty, float $incomingRate, bool $enforceNegative): array
    {
        $oldValue = $state['value'];

        if ($actualQty >= 0) {
            $result = $this->applyInbound($method, $state, $actualQty, $incomingRate);
        } else {
            if ($enforceNegative && ! $this->allowNegativeStock() && ($state['qty'] + $actualQty) < -self::EPSILON) {
                throw new DomainException('Negative stock');
            }

            $result = $this->applyOutbound($method, $state, $actualQty);
        }

        $newState = $result['state'];

        return [
            'state' => $newState,
            'qty_after_transaction' => $newState['qty'],
            'incoming_rate' => $result['incoming_rate'],
            'valuation_rate' => $result['valuation_rate'],
            'stock_value' => $newState['value'],
            'stock_value_difference' => $newState['value'] - $oldValue,
        ];
    }

    /**
     * @param  array{qty: float, value: float, rate: float, queue: list<array{qty: float, rate: float}>}  $state
     * @return array{state: array{qty: float, value: float, rate: float, queue: list<array{qty: float, rate: float}>}, incoming_rate: float, valuation_rate: float}
     */
    private function applyInbound(ValuationMethod $method, array $state, float $actualQty, float $incomingRate): array
    {
        $qtyAfter = $state['qty'] + $actualQty;
        $valueAfter = $state['value'] + ($actualQty * $incomingRate);
        $rateAfter = abs($qtyAfter) > self::EPSILON ? $valueAfter / $qtyAfter : $incomingRate;

        $queue = $state['queue'];

        if ($method === ValuationMethod::FIFO && $actualQty > self::EPSILON) {
            $queue[] = ['qty' => $actualQty, 'rate' => $incomingRate];
        }

        return [
            'state' => [
                'qty' => $qtyAfter,
                'value' => $valueAfter,
                'rate' => $rateAfter,
                'queue' => $queue,
            ],
            'incoming_rate' => $incomingRate,
            'valuation_rate' => $rateAfter,
        ];
    }

    /**
     * @param  array{qty: float, value: float, rate: float, queue: list<array{qty: float, rate: float}>}  $state
     * @return array{state: array{qty: float, value: float, rate: float, queue: list<array{qty: float, rate: float}>}, incoming_rate: float, valuation_rate: float}
     */
    private function applyOutbound(ValuationMethod $method, array $state, float $actualQty): array
    {
        $consumeQty = -1 * $actualQty;
        $qtyAfter = $state['qty'] + $actualQty;

        if ($method === ValuationMethod::FIFO) {
            [$consumedValue, $queue] = $this->consumeFifoLayers($state['queue'], $consumeQty);
            $outgoingRate = $consumeQty > self::EPSILON ? $consumedValue / $consumeQty : $state['rate'];
        } else {
            $outgoingRate = $state['rate'];
            $consumedValue = $consumeQty * $outgoingRate;
            $queue = $state['queue'];
        }

        $valueAfter = $state['value'] - $consumedValue;
        $rateAfter = abs($qtyAfter) > self::EPSILON ? $valueAfter / $qtyAfter : $state['rate'];

        return [
            'state' => [
                'qty' => $qtyAfter,
                'value' => $valueAfter,
                'rate' => $rateAfter,
                'queue' => $queue,
            ],
            'incoming_rate' => 0.0,
            'valuation_rate' => $outgoingRate,
        ];
    }

    /**
     * Consume the oldest FIFO layers first, returning the consumed value and
     * the remaining queue.
     *
     * @param  list<array{qty: float, rate: float}>  $queue
     * @return array{0: float, 1: list<array{qty: float, rate: float}>}
     */
    private function consumeFifoLayers(array $queue, float $consumeQty): array
    {
        $remaining = $consumeQty;
        $consumedValue = 0.0;

        foreach ($queue as $index => $layer) {
            if ($remaining <= self::EPSILON) {
                break;
            }

            $taken = min($layer['qty'], $remaining);
            $consumedValue += $taken * $layer['rate'];
            $remaining -= $taken;
            $queue[$index]['qty'] = $layer['qty'] - $taken;
        }

        // Drop fully consumed layers and re-index the queue.
        $queue = array_values(array_filter(
            $queue,
            fn (array $layer): bool => $layer['qty'] > self::EPSILON
        ));

        // Any quantity not covered by a layer (negative stock) is valued at the
        // last known layer rate, or zero when the queue was empty.
        if ($remaining > self::EPSILON) {
            $fallbackRate = $queue !== [] ? end($queue)['rate'] : 0.0;
            $consumedValue += $remaining * $fallbackRate;
        }

        return [$consumedValue, $queue];
    }

    private function recomputeBin(int $itemId, int $warehouseId): void
    {
        $method = $this->valuationMethodForItem($itemId);
        $stockLedgerEntry = ModelResolver::stockLedgerEntry();

        /** @var Collection<int, Model> $entries */
        $entries = $stockLedgerEntry::query()
            ->where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->where('is_cancelled', false)
            ->orderBy('posting_date')
            ->orderBy('id')
            ->get();

        $state = ['qty' => 0.0, 'value' => 0.0, 'rate' => 0.0, 'queue' => []];

        foreach ($entries as $entry) {
            $actualQty = (float) $entry->getAttribute('actual_qty');
            $incomingRate = (float) $entry->getAttribute('incoming_rate');
            $state = $this->applyMovement($method, $state, $actualQty, $incomingRate, false)['state'];
        }

        $this->writeBin($this->bin($itemId, $warehouseId), $state);
    }

    /**
     * @param  array{qty: float, value: float, rate: float, queue: list<array{qty: float, rate: float}>}  $state
     */
    private function writeBin(Model $bin, array $state): void
    {
        $bin->setAttribute('actual_qty', $state['qty']);
        $bin->setAttribute('valuation_rate', $state['rate']);
        $bin->setAttribute('stock_value', $state['value']);
        $bin->setAttribute('fifo_queue', $state['queue'] === [] ? null : $state['queue']);
        $bin->save();
    }

    private function bin(int $itemId, int $warehouseId): Model
    {
        $bin = ModelResolver::bin();

        /** @var Model $record */
        $record = $bin::query()->firstOrNew([
            'item_id' => $itemId,
            'warehouse_id' => $warehouseId,
        ]);

        return $record;
    }

    /**
     * @return list<array{qty: float, rate: float}>
     */
    private function queueFromBin(Model $bin): array
    {
        $queue = $bin->getAttribute('fifo_queue');

        if (! is_array($queue)) {
            return [];
        }

        return array_values(array_map(
            fn (array $layer): array => ['qty' => (float) $layer['qty'], 'rate' => (float) $layer['rate']],
            $queue
        ));
    }

    /**
     * @param  SubmittableDocument&Model  $voucher
     */
    private function postGeneralLedger(SubmittableDocument $voucher, float $totalDifference): void
    {
        if (! $voucher instanceof PostsStockLedger || abs($totalDifference) <= self::EPSILON) {
            return;
        }

        $accounts = $voucher->stockGlAccounts();
        $stockAccountId = $accounts['stock_account_id'] ?? null;
        $counterAccountId = $accounts['counter_account_id'] ?? null;

        if ($stockAccountId === null || $counterAccountId === null) {
            return;
        }

        $amount = round(abs($totalDifference), 9);

        if ($totalDifference > 0) {
            $entries = [
                ['account_id' => $stockAccountId, 'debit' => $amount, 'credit' => 0],
                ['account_id' => $counterAccountId, 'debit' => 0, 'credit' => $amount],
            ];
        } else {
            $entries = [
                ['account_id' => $counterAccountId, 'debit' => $amount, 'credit' => 0],
                ['account_id' => $stockAccountId, 'debit' => 0, 'credit' => $amount],
            ];
        }

        $this->generalLedger->post($voucher, $entries);
    }

    private function valuationMethodForItem(int $itemId): ValuationMethod
    {
        $item = ModelResolver::item()::query()->find($itemId);

        $method = $item?->getAttribute('valuation_method');

        if ($method instanceof ValuationMethod) {
            return $method;
        }

        if (is_string($method) && ($resolved = ValuationMethod::tryFrom($method)) !== null) {
            return $resolved;
        }

        return ValuationMethod::tryFrom((string) config('erp-stock.default_valuation_method'))
            ?? ValuationMethod::MovingAverage;
    }

    private function allowNegativeStock(): bool
    {
        return (bool) config('erp-stock.allow_negative_stock', false);
    }
}
