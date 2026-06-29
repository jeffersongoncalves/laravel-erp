<?php

namespace JeffersonGoncalves\Erp\Manufacturing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Manufacturing\Support\ModelResolver;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver as StockModelResolver;

/**
 * @property int $id
 * @property int $work_order_id
 * @property string $item_code
 * @property string|null $item_name
 * @property int|null $source_warehouse_id
 * @property float $required_qty
 * @property float $transferred_qty
 * @property float $consumed_qty
 * @property float $rate
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read WorkOrder|null $workOrder
 */
class WorkOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'item_code',
        'item_name',
        'source_warehouse_id',
        'required_qty',
        'transferred_qty',
        'consumed_qty',
        'rate',
    ];

    protected $attributes = [
        'required_qty' => 0,
        'transferred_qty' => 0,
        'consumed_qty' => 0,
        'rate' => 0,
    ];

    protected $casts = [
        'required_qty' => 'float',
        'transferred_qty' => 'float',
        'consumed_qty' => 'float',
        'rate' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-manufacturing.table_prefix') ?? '').'work_order_items';
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::workOrder(), 'work_order_id');
    }

    public function sourceWarehouse(): BelongsTo
    {
        return $this->belongsTo(StockModelResolver::warehouse(), 'source_warehouse_id');
    }
}
