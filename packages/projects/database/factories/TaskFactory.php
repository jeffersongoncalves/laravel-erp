<?php

namespace JeffersonGoncalves\Erp\Projects\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Projects\Models\Task;

/** @extends Factory<Task> */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'subject' => fake()->sentence(4),
            'status' => 'Open',
            'priority' => 'Medium',
            'is_group' => false,
            'company_id' => Company::factory(),
        ];
    }

    public function group(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_group' => true,
        ]);
    }
}
