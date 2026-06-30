<?php

namespace JeffersonGoncalves\Erp\Buying\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $blanket_order_id
 * @property string $item_code
 * @property float $qty
 * @property float $rate
 * @property float $ordered_qty
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read BlanketOrder|null $blanketOrder
 */
class BlanketOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'blanket_order_id',
        'item_code',
        'qty',
        'rate',
        'ordered_qty',
    ];

    protected $attributes = [
        'qty' => 0,
        'rate' => 0,
        'ordered_qty' => 0,
    ];

    protected $casts = [
        'qty' => 'float',
        'rate' => 'float',
        'ordered_qty' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-buying.table_prefix') ?? '').'blanket_order_items';
    }

    public function blanketOrder(): BelongsTo
    {
        return $this->belongsTo(BlanketOrder::class, 'blanket_order_id');
    }
}
