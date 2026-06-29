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
 * @property int $delivery_note_id
 * @property int $item_id
 * @property float $qty
 * @property float $rate
 * @property float $amount
 * @property int $warehouse_id
 * @property string|null $against_sales_order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DeliveryNote|null $deliveryNote
 * @property-read Item|null $item
 */
class DeliveryNoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_note_id',
        'item_id',
        'qty',
        'rate',
        'amount',
        'warehouse_id',
        'against_sales_order',
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
        static::saving(function (DeliveryNoteItem $item): void {
            $item->amount = (float) $item->qty * (float) $item->rate;
        });

        static::saved(fn (DeliveryNoteItem $item) => $item->syncParentTotals());
        static::deleted(fn (DeliveryNoteItem $item) => $item->syncParentTotals());
    }

    public function getTable(): string
    {
        return (config('erp-stock.table_prefix') ?? '').'delivery_note_items';
    }

    public function deliveryNote(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::deliveryNote(), 'delivery_note_id');
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
        $parent = $this->deliveryNote;

        if ($parent === null || $parent->docstatus !== DocStatus::Draft) {
            return;
        }

        $parent->save();
    }
}
