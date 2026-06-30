<?php

namespace JeffersonGoncalves\Erp\Buying\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Buying\Models\Supplier;
use JeffersonGoncalves\Erp\Buying\Models\SupplierScorecard;

/** @extends Factory<SupplierScorecard> */
class SupplierScorecardFactory extends Factory
{
    protected $model = SupplierScorecard::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'name' => fake()->words(2, true),
            'weighting_function' => null,
            'score' => 0,
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
