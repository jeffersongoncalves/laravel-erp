<?php

namespace JeffersonGoncalves\Erp\Stock\Models;

use DomainException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Stock\Models\Contracts\StockLedgerEntryContract;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver;

/**
 * An immutable perpetual-inventory ledger row.
 *
 * Once written, the quantity and value impact of a row never changes: only the
 * `is_cancelled` flag may be toggled (during cancellation). Deletes are
 * forbidden outright.
 *
 * @property int $id
 * @property int $item_id
 * @property int $warehouse_id
 * @property Carbon $posting_date
 * @property float $actual_qty
 * @property float $qty_after_transaction
 * @property float $incoming_rate
 * @property float $valuation_rate
 * @property float $stock_value
 * @property float $stock_value_difference
 * @property string $voucherable_type
 * @property int $voucherable_id
 * @property int|null $company_id
 * @property bool $is_cancelled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Item $item
 * @property-read Warehouse $warehouse
 */
class StockLedgerEntry extends Model implements StockLedgerEntryContract
{
    use HasCompany;
    use HasFactory;

    /** Attributes whose values are frozen once the row exists. */
    private const PROTECTED_ATTRIBUTES = [
        'item_id',
        'warehouse_id',
        'posting_date',
        'actual_qty',
        'qty_after_transaction',
        'incoming_rate',
        'valuation_rate',
        'stock_value',
        'stock_value_difference',
        'voucherable_type',
        'voucherable_id',
        'company_id',
    ];

    protected $fillable = [
        'item_id',
        'warehouse_id',
        'posting_date',
        'actual_qty',
        'qty_after_transaction',
        'incoming_rate',
        'valuation_rate',
        'stock_value',
        'stock_value_difference',
        'voucherable_type',
        'voucherable_id',
        'company_id',
        'is_cancelled',
    ];

    protected $casts = [
        'posting_date' => 'datetime',
        'actual_qty' => 'float',
        'qty_after_transaction' => 'float',
        'incoming_rate' => 'float',
        'valuation_rate' => 'float',
        'stock_value' => 'float',
        'stock_value_difference' => 'float',
        'is_cancelled' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::updating(function (StockLedgerEntry $entry): void {
            foreach (self::PROTECTED_ATTRIBUTES as $attribute) {
                if ($entry->isDirty($attribute)) {
                    throw new DomainException('Stock ledger entries are immutable');
                }
            }
        });

        static::deleting(function (): void {
            throw new DomainException('Stock ledger entries cannot be deleted');
        });
    }

    public function getTable(): string
    {
        return (config('erp-stock.table_prefix') ?? '').'stock_ledger_entries';
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::item(), 'item_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::warehouse(), 'warehouse_id');
    }

    public function voucherable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_cancelled', false);
    }
}
