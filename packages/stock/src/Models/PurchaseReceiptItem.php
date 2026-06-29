<?php

namespace JeffersonGoncalves\Erp\Stock\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver;

/**
 * @property int $id
 * @property int $purchase_receipt_id
 * @property int $item_id
 * @property float $qty
 * @property float $rate
 * @property float $amount
 * @property int $warehouse_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read PurchaseReceipt|null $purchaseReceipt
 * @property-read Item|null $item
 */
class PurchaseReceiptItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_receipt_id',
        'item_id',
        'qty',
        'rate',
        'amount',
        'warehouse_id',
    ];

    protected $attributes = [
        'qty' => 0,
        'rate' => 0,
        'amount' => 0,
    ];

    protected $casts = [
        'qty' => 'float',
        'rate' => 'float',
        'amount' => 'float',
    ];

    protected static function booted(): void
    {
        static::saving(function (PurchaseReceiptItem $item): void {
            $item->amount = (float) $item->qty * (float) $item->rate;
        });

        static::saved(fn (PurchaseReceiptItem $item) => $item->syncParentTotals());
        static::deleted(fn (PurchaseReceiptItem $item) => $item->syncParentTotals());
    }

    public function getTable(): string
    {
        return (config('erp-stock.table_prefix') ?? '').'purchase_receipt_items';
    }

    public function purchaseReceipt(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::purchaseReceipt(), 'purchase_receipt_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::item(), 'item_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::warehouse(), 'warehouse_id');
    }

    protected function syncParentTotals(): void
    {
        $parent = $this->purchaseReceipt;

        if ($parent === null || $parent->docstatus !== DocStatus::Draft) {
            return;
        }

        $parent->save();
    }
}
