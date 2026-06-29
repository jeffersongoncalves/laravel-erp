<?php

namespace JeffersonGoncalves\Erp\Core\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Uom;

/** @extends Factory<Uom> */
class UomFactory extends Factory
{
    protected $model = Uom::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Nos', 'Box', 'Kg', 'Litre', 'Meter', 'Unit', 'Pair', 'Set']).'-'.fake()->unique()->numberBetween(1, 9999),
            'enabled' => true,
            'must_be_whole_number' => false,
        ];
    }

    public function wholeNumber(): static
    {
        return $this->state(fn (array $attributes) => [
            'must_be_whole_number' => true,
        ]);
    }
}
