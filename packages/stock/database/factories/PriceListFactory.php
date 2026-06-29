<?php

namespace JeffersonGoncalves\Erp\Stock\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Stock\Models\PriceList;

/** @extends Factory<PriceList> */
class PriceListFactory extends Factory
{
    protected $model = PriceList::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'currency' => 'USD',
            'enabled' => true,
            'is_selling' => false,
            'is_buying' => false,
        ];
    }

    public function selling(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_selling' => true,
        ]);
    }

    public function buying(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_buying' => true,
        ]);
    }
}
