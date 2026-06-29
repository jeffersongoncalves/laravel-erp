<?php

namespace JeffersonGoncalves\Erp\Manufacturing\Services;

use InvalidArgumentException;
use JeffersonGoncalves\Erp\Manufacturing\Models\WorkOrder;
use JeffersonGoncalves\Erp\Stock\Enums\StockEntryType;
use JeffersonGoncalves\Erp\Stock\Models\StockEntry;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver as StockModelResolver;

/**
 * Turns a work order into the stock movement that actually consumes raw
 * materials and produces the finished good.
 */
class WorkOrderService
{
    /**
     * Build a Draft Manufacture stock entry for a work order.
     *
     * Each required material becomes an outbound detail line (consumed from the
     * work-in-progress / source warehouse) and the production item becomes a
     * single inbound finished-good line (received into the finished-goods
     * warehouse). The entry is returned in Draft: submitting it runs the stock
     * engine, which writes the outbound raw and inbound finished-good
     * stock-ledger entries and the matching general-ledger impact.
     *
     * The produced quantity / status of the (immutable, already-submitted) work
     * order are intentionally left untouched here; advancing them is a documented
     * follow-up once a work order supports partial completion.
     */
    public function manufacture(WorkOrder $wo, ?int $wipWarehouseId = null, ?int $fgWarehouseId = null): StockEntry
    {
        $wip = $wipWarehouseId
            ?? $wo->wip_warehouse_id
            ?? config('erp-manufacturing.default_wip_warehouse');

        $fg = $fgWarehouseId
            ?? $wo->fg_warehouse_id
            ?? config('erp-manufacturing.default_fg_warehouse');

        $entry = new StockEntry([
            'stock_entry_type' => StockEntryType::Manufacture,
            'posting_date' => now(),
            'company_id' => $wo->company_id,
        ]);
        $entry->save();

        foreach ($wo->requiredItems as $material) {
            $entry->items()->create([
                'item_id' => $this->resolveItemId($material->item_code),
                's_warehouse_id' => $material->source_warehouse_id ?? $wip,
                'qty' => (float) $material->required_qty,
                'basic_rate' => (float) $material->rate,
            ]);
        }

        $entry->items()->create([
            'item_id' => $this->resolveItemId($wo->production_item),
            't_warehouse_id' => $fg,
            'qty' => (float) $wo->qty,
        ]);

        return $entry->refresh();
    }

    /**
     * Resolve a stock item id from its item code.
     *
     * @throws InvalidArgumentException when no item matches the code.
     */
    protected function resolveItemId(string $itemCode): int
    {
        $item = StockModelResolver::item()::query()
            ->where('item_code', $itemCode)
            ->first();

        if ($item === null) {
            throw new InvalidArgumentException("No stock item found for item code [{$itemCode}].");
        }

        return (int) $item->getAttribute('id');
    }
}
