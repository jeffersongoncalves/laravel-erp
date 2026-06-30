<?php

namespace JeffersonGoncalves\Erp\Hr\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Hr\Models\LeaveType;

/** @extends Factory<LeaveType> */
class LeaveTypeFactory extends Factory
{
    protected $model = LeaveType::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'max_leaves_allowed' => fake()->numberBetween(5, 30),
            'is_paid' => true,
            'allow_negative' => false,
        ];
    }

    public function allowNegative(): static
    {
        return $this->state(fn (array $attributes) => [
            'allow_negative' => true,
        ]);
    }
}
