<?php

namespace JeffersonGoncalves\Erp\Selling\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Selling\Support\ModelResolver;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver as StockModelResolver;

/**
 * @property int $id
 * @property int $sales_order_id
 * @property string $item_code
 * @property string|null $item_name
 * @property string|null $description
 * @property float $qty
 * @property float $rate
 * @property float $amount
 * @property int|null $warehouse_id
 * @property float $delivered_qty
 * @property float $billed_qty
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read SalesOrder|null $salesOrder
 */
class SalesOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_order_id',
        'item_code',
        'item_name',
        'description',
        'qty',
        'rate',
        'amount',
        'warehouse_id',
        'delivered_qty',
        'billed_qty',
    ];

    protected $attributes = [
        'qty' => 1,
        'rate' => 0,
        'amount' => 0,
        'delivered_qty' => 0,
        'billed_qty' => 0,
    ];

    protected $casts = [
        'qty' => 'float',
        'rate' => 'float',
        'amount' => 'float',
        'delivered_qty' => 'float',
        'billed_qty' => 'float',
    ];

    protected static function booted(): void
    {
        static::saving(function (SalesOrderItem $item): void {
            $item->amount = (float) $item->qty * (float) $item->rate;
        });

        static::saved(fn (SalesOrderItem $item) => $item->syncParentTotals());
        static::deleted(fn (SalesOrderItem $item) => $item->syncParentTotals());
    }

    public function getTable(): string
    {
        return (config('erp-selling.table_prefix') ?? '').'sales_order_items';
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::salesOrder(), 'sales_order_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(StockModelResolver::warehouse(), 'warehouse_id');
    }

    protected function syncParentTotals(): void
    {
        $parent = $this->salesOrder;

        if ($parent === null || $parent->docstatus !== DocStatus::Draft) {
            return;
        }

        $parent->save();
    }
}
