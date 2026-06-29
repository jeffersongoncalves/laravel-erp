<?php

namespace JeffersonGoncalves\Erp\Quality\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Quality\Models\QualityGoal;

/** @extends Factory<QualityGoal> */
class QualityGoalFactory extends Factory
{
    protected $model = QualityGoal::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'goal' => ucfirst(fake()->unique()->words(3, true)),
            'procedure' => fake()->optional()->words(2, true),
            'frequency' => fake()->randomElement(['None', 'Daily', 'Weekly', 'Monthly', 'Quarterly', 'Yearly']),
        ];
    }
}
