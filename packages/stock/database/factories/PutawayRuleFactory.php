<?php

namespace JeffersonGoncalves\Erp\Stock\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Stock\Models\Item;
use JeffersonGoncalves\Erp\Stock\Models\PutawayRule;
use JeffersonGoncalves\Erp\Stock\Models\Warehouse;

/** @extends Factory<PutawayRule> */
class PutawayRuleFactory extends Factory
{
    protected $model = PutawayRule::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'warehouse_id' => Warehouse::factory(),
            'company_id' => Company::factory(),
            'capacity' => fake()->randomFloat(2, 10, 500),
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
