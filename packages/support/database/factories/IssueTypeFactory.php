<?php

namespace JeffersonGoncalves\Erp\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Support\Models\IssueType;

/** @extends Factory<IssueType> */
class IssueTypeFactory extends Factory
{
    protected $model = IssueType::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Bug', 'Question', 'Incident', 'Feature Request',
                'Installation', 'Maintenance', 'Complaint', 'Hardware',
            ]).' '.fake()->unique()->numberBetween(1, 9999),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
