<?php

namespace JeffersonGoncalves\Erp\Buying\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Buying\Models\SupplierGroup;

/** @extends Factory<SupplierGroup> */
class SupplierGroupFactory extends Factory
{
    protected $model = SupplierGroup::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'is_group' => false,
        ];
    }

    public function group(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_group' => true,
        ]);
    }
}
