<?php

namespace JeffersonGoncalves\Erp\Projects\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Projects\Models\ActivityType;

/** @extends Factory<ActivityType> */
class ActivityTypeFactory extends Factory
{
    protected $model = ActivityType::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->jobTitle(),
            'default_costing_rate' => fake()->randomFloat(2, 10, 80),
            'default_billing_rate' => fake()->randomFloat(2, 40, 160),
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
