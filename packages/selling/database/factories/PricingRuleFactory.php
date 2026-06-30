<?php

namespace JeffersonGoncalves\Erp\Selling\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Selling\Models\PricingRule;

/** @extends Factory<PricingRule> */
class PricingRuleFactory extends Factory
{
    protected $model = PricingRule::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'company_id' => Company::factory(),
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
    }

    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'disabled' => true,
        ]);
    }
}
