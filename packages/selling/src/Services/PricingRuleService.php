<?php

namespace JeffersonGoncalves\Erp\Selling\Services;

use JeffersonGoncalves\Erp\Selling\Enums\PricingRuleRateOrDiscount;
use JeffersonGoncalves\Erp\Selling\Models\PricingRule;

/**
 * Resolves the best-matching pricing rule for a line and applies its rate or
 * discount to a base price. Pure computation: no documents are mutated.
 */
class PricingRuleService
{
    /**
     * Find the highest-priority active pricing rule that targets the given item,
     * whose quantity band contains $qty and whose validity window contains $date.
     */
    public function resolve(string $itemCode, float $qty, string $date): ?PricingRule
    {
        return PricingRule::query()
            ->where('disabled', false)
            ->whereHas('items', fn ($query) => $query->where('item_code', $itemCode))
            ->where('min_qty', '<=', $qty)
            ->where(fn ($query) => $query->where('max_qty', 0)->orWhere('max_qty', '>=', $qty))
            ->where(fn ($query) => $query->whereNull('valid_from')->orWhere('valid_from', '<=', $date))
            ->where(fn ($query) => $query->whereNull('valid_upto')->orWhere('valid_upto', '>=', $date))
            ->orderByDesc('priority')
            ->first();
    }

    /**
     * Apply a pricing rule to a base price, honoring its rate-or-discount mode:
     * a flat rate, a percentage discount, or a fixed-amount discount.
     */
    public function applyTo(PricingRule $rule, float $basePrice): float
    {
        return match ($rule->rate_or_discount) {
            PricingRuleRateOrDiscount::Rate => (float) $rule->rate,
            PricingRuleRateOrDiscount::DiscountPercentage => $basePrice - ($basePrice * (float) $rule->discount_percentage / 100),
            PricingRuleRateOrDiscount::DiscountAmount => $basePrice - (float) $rule->discount_amount,
        };
    }
}
