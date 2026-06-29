<?php

namespace JeffersonGoncalves\Erp\Stock\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Stock\Models\Contracts\BinContract;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver;

/**
 * The live, per (item, warehouse) stock balance maintained by the perpetual
 * inventory engine.
 *
 * @property int $id
 * @property int $item_id
 * @property int $warehouse_id
 * @property float $actual_qty
 * @property float $valuation_rate
 * @property float $stock_value
 * @property float $reserved_qty
 * @property float $ordered_qty
 * @property array<int, array{qty: float, rate: float}>|null $fifo_queue
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Item|null $item
 * @property-read Warehouse|null $warehouse
 */
class Bin extends Model implements BinContract
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'warehouse_id',
        'actual_qty',
        'valuation_rate',
        'stock_value',
        'reserved_qty',
        'ordered_qty',
        'fifo_queue',
    ];

    protected $attributes = [
        'actual_qty' => 0,
        'valuation_rate' => 0,
        'stock_value' => 0,
        'reserved_qty' => 0,
        'ordered_qty' => 0,
    ];

    protected $casts = [
        'actual_qty' => 'float',
        'valuation_rate' => 'float',
        'stock_value' => 'float',
        'reserved_qty' => 'float',
        'ordered_qty' => 'float',
        'fifo_queue' => 'array',
    ];

    public function getTable(): string
    {
        return (config('erp-stock.table_prefix') ?? '').'bins';
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::item(), 'item_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::warehouse(), 'warehouse_id');
    }
}
