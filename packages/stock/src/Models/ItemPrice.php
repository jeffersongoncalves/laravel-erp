<?php

namespace JeffersonGoncalves\Erp\Stock\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver;

/**
 * @property int $id
 * @property int $item_id
 * @property int $price_list_id
 * @property float $rate
 * @property string $currency
 * @property Carbon|null $valid_from
 * @property Carbon|null $valid_upto
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Item|null $item
 * @property-read PriceList|null $priceList
 */
class ItemPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'price_list_id',
        'rate',
        'currency',
        'valid_from',
        'valid_upto',
    ];

    protected $attributes = [
        'rate' => 0,
        'currency' => 'USD',
    ];

    protected $casts = [
        'rate' => 'float',
        'valid_from' => 'date',
        'valid_upto' => 'date',
    ];

    public function getTable(): string
    {
        return (config('erp-stock.table_prefix') ?? '').'item_prices';
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::item(), 'item_id');
    }

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::priceList(), 'price_list_id');
    }
}
