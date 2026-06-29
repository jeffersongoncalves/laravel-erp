<?php

namespace JeffersonGoncalves\Erp\Quality\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Quality\Models\QualityProcedure;

/** @extends Factory<QualityProcedure> */
class QualityProcedureFactory extends Factory
{
    protected $model = QualityProcedure::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'quality_procedure_name' => ucfirst(fake()->unique()->words(3, true)),
            'is_group' => false,
            'process_owner' => fake()->optional()->name(),
        ];
    }

    public function group(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_group' => true,
        ]);
    }
}
