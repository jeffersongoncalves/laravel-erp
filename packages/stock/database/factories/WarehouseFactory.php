<?php

namespace JeffersonGoncalves\Erp\Stock\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Stock\Models\Warehouse;

/** @extends Factory<Warehouse> */
class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->city().' Warehouse',
            'is_group' => false,
            'company_id' => Company::factory(),
            'disabled' => false,
        ];
    }

    public function group(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_group' => true,
        ]);
    }
}
