<?php

namespace JeffersonGoncalves\Erp\Projects\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Projects\Models\Project;

/** @extends Factory<Project> */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'project_name' => fake()->unique()->catchPhrase(),
            'status' => 'Open',
            'party_type' => 'Customer',
            'customer_name' => fake()->company(),
            'expected_start_date' => fake()->date(),
            'company_id' => Company::factory(),
        ];
    }
}
