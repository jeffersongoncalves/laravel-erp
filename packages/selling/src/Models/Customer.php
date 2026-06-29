<?php

namespace JeffersonGoncalves\Erp\Selling\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Support\ModelResolver as CoreModelResolver;
use JeffersonGoncalves\Erp\Selling\Models\Contracts\CustomerContract;
use JeffersonGoncalves\Erp\Selling\Support\ModelResolver;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver as StockModelResolver;

/**
 * @property int $id
 * @property string $customer_name
 * @property int|null $customer_group_id
 * @property string|null $territory
 * @property string $customer_type
 * @property string $default_currency
 * @property int|null $default_price_list_id
 * @property string|null $tax_id
 * @property float $credit_limit
 * @property bool $disabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read CustomerGroup|null $customerGroup
 * @property-read Collection<int, Model> $addresses
 * @property-read Collection<int, Model> $contacts
 */
class Customer extends Model implements CustomerContract
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_group_id',
        'territory',
        'customer_type',
        'default_currency',
        'default_price_list_id',
        'tax_id',
        'credit_limit',
        'disabled',
    ];

    protected $attributes = [
        'customer_type' => 'Company',
        'default_currency' => 'USD',
        'credit_limit' => 0,
        'disabled' => false,
    ];

    protected $casts = [
        'credit_limit' => 'float',
        'disabled' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-selling.table_prefix') ?? '').'customers';
    }

    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::customerGroup(), 'customer_group_id');
    }

    public function defaultPriceList(): BelongsTo
    {
        return $this->belongsTo(StockModelResolver::priceList(), 'default_price_list_id');
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(CoreModelResolver::address(), 'addressable');
    }

    public function contacts(): MorphMany
    {
        return $this->morphMany(CoreModelResolver::contact(), 'contactable');
    }

    /** @param  Builder<static>  $query */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('disabled', false);
    }
}
