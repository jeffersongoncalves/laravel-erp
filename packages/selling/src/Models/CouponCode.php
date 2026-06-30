<?php

namespace JeffersonGoncalves\Erp\Selling\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $coupon_name
 * @property string $coupon_code
 * @property int|null $pricing_rule_id
 * @property Carbon|null $valid_from
 * @property Carbon|null $valid_upto
 * @property int $maximum_use
 * @property int $used
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read PricingRule|null $pricingRule
 */
class CouponCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_name',
        'coupon_code',
        'pricing_rule_id',
        'valid_from',
        'valid_upto',
        'maximum_use',
        'used',
    ];

    protected $attributes = [
        'maximum_use' => 0,
        'used' => 0,
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_upto' => 'date',
        'maximum_use' => 'integer',
        'used' => 'integer',
    ];

    public function getTable(): string
    {
        return (config('erp-selling.table_prefix') ?? '').'coupon_codes';
    }

    public function pricingRule(): BelongsTo
    {
        return $this->belongsTo(PricingRule::class, 'pricing_rule_id');
    }
}
