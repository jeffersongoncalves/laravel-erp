<?php

namespace JeffersonGoncalves\Erp\Stock\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
use JeffersonGoncalves\Erp\Stock\Enums\StockEntryType;
use JeffersonGoncalves\Erp\Stock\Services\StockLedgerService;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver;

/**
 * @property int $id
 * @property string|null $naming_series
 * @property StockEntryType $stock_entry_type
 * @property Carbon $posting_date
 * @property int|null $company_id
 * @property int|null $from_warehouse_id
 * @property int|null $to_warehouse_id
 * @property float $total_outgoing_value
 * @property float $total_incoming_value
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Warehouse|null $fromWarehouse
 * @property-read Warehouse|null $toWarehouse
 * @property-read Collection<int, StockEntryDetail> $items
 */
class StockEntry extends Model implements PostsStockLedger, PostsToLedger, SubmittableDocument
{
    use HasCompany;
    use HasFactory;
    use HasNamingSeries;
    use IsSubmittable;
    use ResolvesStockGlAccounts;

    /** The counter account (e.g. temporary stock difference) for the GL impact. */
    public ?int $counterAccountId = null;

    protected $fillable = [
        'naming_series',
        'stock_entry_type',
        'posting_date',
        'company_id',
        'from_warehouse_id',
        'to_warehouse_id',
        'total_outgoing_value',
        'total_incoming_value',
        'docstatus',
    ];

    protected $attributes = [
        'total_outgoing_value' => 0,
        'total_incoming_value' => 0,
        'docstatus' => 0,
    ];

    protected $casts = [
        'stock_entry_type' => StockEntryType::class,
        'posting_date' => 'datetime',
        'total_outgoing_value' => 'float',
        'total_incoming_value' => 'float',
        'docstatus' => DocStatus::class,
    ];

    protected static function booted(): void
    {
        static::saving(function (StockEntry $entry): void {
            if ($entry->docstatus === DocStatus::Draft) {
                $entry->calculateTotals();
            }
        });
    }

    public function getTable(): string
    {
        return (config('erp-stock.table_prefix') ?? '').'stock_entries';
    }

    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::warehouse(), 'from_warehouse_id');
    }

    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::warehouse(), 'to_warehouse_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ModelResolver::stockEntryDetail(), 'stock_entry_id');
    }

    public function calculateTotals(): void
    {
        if (! $this->exists) {
            return;
        }

        $incoming = 0.0;
        $outgoing = 0.0;

        foreach ($this->items as $item) {
            if (($item->t_warehouse_id ?? $this->to_warehouse_id) !== null && $this->stock_entry_type !== StockEntryType::MaterialIssue) {
                $incoming += (float) $item->amount;
            }

            if (($item->s_warehouse_id ?? $this->from_warehouse_id) !== null && $this->stock_entry_type !== StockEntryType::MaterialReceipt) {
                $outgoing += (float) $item->amount;
            }
        }

        $this->total_incoming_value = $incoming;
        $this->total_outgoing_value = $outgoing;
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
        $warehouseId = $this->to_warehouse_id ?? $this->from_warehouse_id;

        return [
            'stock_account_id' => $this->warehouseAccountId($warehouseId),
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
            $source = $item->s_warehouse_id ?? $this->from_warehouse_id;
            $target = $item->t_warehouse_id ?? $this->to_warehouse_id;
            $qty = (float) $item->qty;
            $rate = (float) $item->basic_rate;

            $isOutbound = $this->stock_entry_type !== StockEntryType::MaterialReceipt && $source !== null;
            $isInbound = $this->stock_entry_type !== StockEntryType::MaterialIssue && $target !== null;

            if ($isOutbound && $rate <= 0.0) {
                $rate = $this->currentValuationRate((int) $item->item_id, (int) $source);
            }

            if ($isOutbound) {
                $movements[] = [
                    'item_id' => (int) $item->item_id,
                    'warehouse_id' => (int) $source,
                    'actual_qty' => -1 * $qty,
                    'incoming_rate' => 0.0,
                    'posting_date' => $this->posting_date,
                ];
            }

            if ($isInbound) {
                $movements[] = [
                    'item_id' => (int) $item->item_id,
                    'warehouse_id' => (int) $target,
                    'actual_qty' => $qty,
                    'incoming_rate' => $rate,
                    'posting_date' => $this->posting_date,
                ];
            }
        }

        return $movements;
    }

    protected function currentValuationRate(int $itemId, int $warehouseId): float
    {
        $bin = ModelResolver::bin()::query()
            ->where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->first();

        return $bin !== null ? (float) $bin->getAttribute('valuation_rate') : 0.0;
    }
}
