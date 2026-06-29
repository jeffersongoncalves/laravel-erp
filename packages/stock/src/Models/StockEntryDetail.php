<?php

namespace JeffersonGoncalves\Erp\Stock\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Core\Support\ModelResolver as CoreModelResolver;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver;

/**
 * @property int $id
 * @property int $stock_entry_id
 * @property int $item_id
 * @property int|null $s_warehouse_id
 * @property int|null $t_warehouse_id
 * @property float $qty
 * @property int|null $uom_id
 * @property float $basic_rate
 * @property float $valuation_rate
 * @property float $amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read StockEntry|null $stockEntry
 * @property-read Item|null $item
 */
class StockEntryDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_entry_id',
        'item_id',
        's_warehouse_id',
        't_warehouse_id',
        'qty',
        'uom_id',
        'basic_rate',
        'valuation_rate',
        'amount',
    ];

    protected $attributes = [
        'qty' => 0,
        'basic_rate' => 0,
        'valuation_rate' => 0,
        'amount' => 0,
    ];

    protected $casts = [
        'qty' => 'float',
        'basic_rate' => 'float',
        'valuation_rate' => 'float',
        'amount' => 'float',
    ];

    protected static function booted(): void
    {
        static::saving(function (StockEntryDetail $item): void {
            $item->amount = (float) $item->qty * (float) $item->basic_rate;
        });

        static::saved(fn (StockEntryDetail $item) => $item->syncParentTotals());
        static::deleted(fn (StockEntryDetail $item) => $item->syncParentTotals());
    }

    public function getTable(): string
    {
        return (config('erp-stock.table_prefix') ?? '').'stock_entry_details';
    }

    public function stockEntry(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::stockEntry(), 'stock_entry_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::item(), 'item_id');
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(CoreModelResolver::uom(), 'uom_id');
    }

    protected function syncParentTotals(): void
    {
        $parent = $this->stockEntry;

        if ($parent === null || $parent->docstatus !== DocStatus::Draft) {
            return;
        }

        $parent->save();
    }
}
