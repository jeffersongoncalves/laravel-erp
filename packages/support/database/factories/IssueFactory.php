<?php

namespace JeffersonGoncalves\Erp\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Support\Models\Issue;

/** @extends Factory<Issue> */
class IssueFactory extends Factory
{
    protected $model = Issue::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'subject' => fake()->sentence(),
            'party_type' => 'Customer',
            'customer_name' => fake()->optional()->company(),
            'raised_by' => fake()->optional()->safeEmail(),
            'status' => 'Open',
            'priority' => 'Medium',
            'opening_date' => fake()->date(),
            'company_id' => Company::factory(),
        ];
    }
}
