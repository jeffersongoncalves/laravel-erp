<?php

namespace JeffersonGoncalves\Erp\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Crm\Models\Lead;

/** @extends Factory<Lead> */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'lead_name' => fake()->name(),
            'company_name' => fake()->optional()->company(),
            'email' => fake()->optional()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'mobile_no' => fake()->optional()->phoneNumber(),
            'status' => 'Lead',
            'territory' => fake()->optional()->country(),
            'company_id' => Company::factory(),
        ];
    }
}
