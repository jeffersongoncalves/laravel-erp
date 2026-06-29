<?php

namespace JeffersonGoncalves\Erp\Stock\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Stock\Models\Contracts\PriceListContract;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver;

/**
 * @property int $id
 * @property string $name
 * @property string $currency
 * @property bool $enabled
 * @property bool $is_selling
 * @property bool $is_buying
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class PriceList extends Model implements PriceListContract
{
    use HasFactory;

    protected $fillable = [
        'name',
        'currency',
        'enabled',
        'is_selling',
        'is_buying',
    ];

    protected $attributes = [
        'currency' => 'USD',
        'enabled' => true,
        'is_selling' => false,
        'is_buying' => false,
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'is_selling' => 'boolean',
        'is_buying' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-stock.table_prefix') ?? '').'price_lists';
    }

    public function itemPrices(): HasMany
    {
        return $this->hasMany(ModelResolver::itemPrice(), 'price_list_id');
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }
}
