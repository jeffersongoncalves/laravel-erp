<?php

namespace JeffersonGoncalves\Erp\Selling\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Selling\Enums\PricingRuleApplyOn;
use JeffersonGoncalves\Erp\Selling\Enums\PricingRulePriceOrProductDiscount;
use JeffersonGoncalves\Erp\Selling\Enums\PricingRuleRateOrDiscount;

/**
 * @property int $id
 * @property string $name
 * @property int|null $company_id
 * @property PricingRuleApplyOn $apply_on
 * @property PricingRulePriceOrProductDiscount $price_or_product_discount
 * @property PricingRuleRateOrDiscount $rate_or_discount
 * @property float $rate
 * @property float $discount_percentage
 * @property float $discount_amount
 * @property float $min_qty
 * @property float $max_qty
 * @property Carbon|null $valid_from
 * @property Carbon|null $valid_upto
 * @property int $priority
 * @property bool $disabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, PricingRuleItem> $items
 */
class PricingRule extends Model
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'name',
        'company_id',
        'apply_on',
        'price_or_product_discount',
        'rate_or_discount',
        'rate',
        'discount_percentage',
        'discount_amount',
        'min_qty',
        'max_qty',
        'valid_from',
        'valid_upto',
        'priority',
        'disabled',
    ];

    protected $attributes = [
        'apply_on' => 'Item',
        'price_or_product_discount' => 'Price',
        'rate_or_discount' => 'Rate',
        'rate' => 0,
        'discount_percentage' => 0,
        'discount_amount' => 0,
        'min_qty' => 0,
        'max_qty' => 0,
        'priority' => 1,
        'disabled' => false,
    ];

    protected $casts = [
        'apply_on' => PricingRuleApplyOn::class,
        'price_or_product_discount' => PricingRulePriceOrProductDiscount::class,
        'rate_or_discount' => PricingRuleRateOrDiscount::class,
        'rate' => 'float',
        'discount_percentage' => 'float',
        'discount_amount' => 'float',
        'min_qty' => 'float',
        'max_qty' => 'float',
        'valid_from' => 'date',
        'valid_upto' => 'date',
        'priority' => 'integer',
        'disabled' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-selling.table_prefix') ?? '').'pricing_rules';
    }

    public function items(): HasMany
    {
        return $this->hasMany(PricingRuleItem::class, 'pricing_rule_id');
    }

    /** @param  Builder<static>  $query */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('disabled', false);
    }
}
