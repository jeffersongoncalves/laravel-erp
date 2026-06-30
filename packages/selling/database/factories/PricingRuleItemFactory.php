<?php

namespace JeffersonGoncalves\Erp\Selling\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Selling\Models\PricingRule;
use JeffersonGoncalves\Erp\Selling\Models\PricingRuleItem;

/** @extends Factory<PricingRuleItem> */
class PricingRuleItemFactory extends Factory
{
    protected $model = PricingRuleItem::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'pricing_rule_id' => PricingRule::factory(),
            'item_code' => fake()->unique()->bothify('ITEM-####'),
        ];
    }
}
