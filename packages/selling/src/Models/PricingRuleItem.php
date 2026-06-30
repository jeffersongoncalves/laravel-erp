<?php

namespace JeffersonGoncalves\Erp\Selling\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $pricing_rule_id
 * @property string $item_code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read PricingRule|null $pricingRule
 */
class PricingRuleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pricing_rule_id',
        'item_code',
    ];

    public function getTable(): string
    {
        return (config('erp-selling.table_prefix') ?? '').'pricing_rule_items';
    }

    public function pricingRule(): BelongsTo
    {
        return $this->belongsTo(PricingRule::class, 'pricing_rule_id');
    }
}
