<?php

namespace JeffersonGoncalves\Erp\Manufacturing\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Manufacturing\Models\Workstation;

/** @extends Factory<Workstation> */
class WorkstationFactory extends Factory
{
    protected $model = Workstation::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true).' Workstation',
            'hour_rate' => fake()->randomFloat(2, 10, 200),
            'production_capacity' => fake()->numberBetween(1, 5),
        ];
    }
}
