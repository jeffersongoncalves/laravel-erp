<?php

namespace JeffersonGoncalves\Erp\Stock\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Concerns\HasNamingSeries;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\PostsToLedger;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Stock\Concerns\ResolvesStockGlAccounts;
use JeffersonGoncalves\Erp\Stock\Contracts\PostsStockLedger;
use JeffersonGoncalves\Erp\Stock\Services\StockLedgerService;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver;

/**
 * Sets the absolute on-hand quantity of items to a counted target, posting the
 * difference (positive or negative) against the inventory account.
 *
 * @property int $id
 * @property string|null $naming_series
 * @property Carbon $posting_date
 * @property int|null $company_id
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, StockReconciliationItem> $items
 */
class StockReconciliation extends Model implements PostsStockLedger, PostsToLedger, SubmittableDocument
{
    use HasCompany;
    use HasFactory;
    use HasNamingSeries;
    use IsSubmittable;
    use ResolvesStockGlAccounts;

    /** The stock-adjustment account the difference is posted against. */
    public ?int $counterAccountId = null;

    protected $fillable = [
        'naming_series',
        'posting_date',
        'company_id',
        'docstatus',
    ];

    protected $attributes = [
        'docstatus' => 0,
    ];

    protected $casts = [
        'posting_date' => 'datetime',
        'docstatus' => DocStatus::class,
    ];

    public function getTable(): string
    {
        return (config('erp-stock.table_prefix') ?? '').'stock_reconciliations';
    }

    public function items(): HasMany
    {
        return $this->hasMany(ModelResolver::stockReconciliationItem(), 'stock_reconciliation_id');
    }

    public function postLedgerEntries(): void
    {
        app(StockLedgerService::class)->post($this, $this->buildMovements());
    }

    public function reverseLedgerEntries(): void
    {
        app(StockLedgerService::class)->reverse($this);
    }

    public function stockGlAccounts(): array
    {
        return [
            'stock_account_id' => $this->warehouseAccountId($this->primaryWarehouseId()),
            'counter_account_id' => $this->counterAccountId,
        ];
    }

    /**
     * @return list<array{item_id: int, warehouse_id: int, actual_qty: float, incoming_rate: float, posting_date: mixed}>
     */
    protected function buildMovements(): array
    {
        $movements = [];

        foreach ($this->items as $item) {
            $current = $this->currentQty((int) $item->item_id, (int) $item->warehouse_id);
            $delta = (float) $item->qty - $current;

            if (abs($delta) <= 0.000000001) {
                continue;
            }

            $movements[] = [
                'item_id' => (int) $item->item_id,
                'warehouse_id' => (int) $item->warehouse_id,
                'actual_qty' => $delta,
                'incoming_rate' => $delta > 0 ? (float) $item->valuation_rate : 0.0,
                'posting_date' => $this->posting_date,
            ];
        }

        return $movements;
    }

    protected function currentQty(int $itemId, int $warehouseId): float
    {
        $bin = ModelResolver::bin()::query()
            ->where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->first();

        return $bin !== null ? (float) $bin->getAttribute('actual_qty') : 0.0;
    }

    protected function primaryWarehouseId(): ?int
    {
        $first = $this->items->first();

        return $first?->warehouse_id !== null ? (int) $first->warehouse_id : null;
    }
}
