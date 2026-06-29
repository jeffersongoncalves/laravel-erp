<?php

namespace JeffersonGoncalves\Erp\Buying\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Buying\Support\ModelResolver;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver as StockModelResolver;

/**
 * @property int $id
 * @property int $purchase_order_id
 * @property string $item_code
 * @property string|null $item_name
 * @property string|null $description
 * @property float $qty
 * @property float $rate
 * @property float $amount
 * @property int|null $warehouse_id
 * @property float $received_qty
 * @property float $billed_qty
 * @property Carbon|null $schedule_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read PurchaseOrder|null $purchaseOrder
 */
class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'item_code',
        'item_name',
        'description',
        'qty',
        'rate',
        'amount',
        'warehouse_id',
        'received_qty',
        'billed_qty',
        'schedule_date',
    ];

    protected $attributes = [
        'qty' => 1,
        'rate' => 0,
        'amount' => 0,
        'received_qty' => 0,
        'billed_qty' => 0,
    ];

    protected $casts = [
        'qty' => 'float',
        'rate' => 'float',
        'amount' => 'float',
        'received_qty' => 'float',
        'billed_qty' => 'float',
        'schedule_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::saving(function (PurchaseOrderItem $item): void {
            $item->amount = (float) $item->qty * (float) $item->rate;
        });

        static::saved(fn (PurchaseOrderItem $item) => $item->syncParentTotals());
        static::deleted(fn (PurchaseOrderItem $item) => $item->syncParentTotals());
    }

    public function getTable(): string
    {
        return (config('erp-buying.table_prefix') ?? '').'purchase_order_items';
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::purchaseOrder(), 'purchase_order_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(StockModelResolver::warehouse(), 'warehouse_id');
    }

    protected function syncParentTotals(): void
    {
        $parent = $this->purchaseOrder;

        if ($parent === null || $parent->docstatus !== DocStatus::Draft) {
            return;
        }

        $parent->save();
    }
}
