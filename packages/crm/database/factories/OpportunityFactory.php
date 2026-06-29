<?php

namespace JeffersonGoncalves\Erp\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Crm\Models\Opportunity;

/** @extends Factory<Opportunity> */
class OpportunityFactory extends Factory
{
    protected $model = Opportunity::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'opportunity_from' => 'Lead',
            'party_type' => 'Customer',
            'party_name' => fake()->company(),
            'status' => 'Open',
            'opportunity_type' => 'Sales',
            'probability' => fake()->numberBetween(0, 100),
            'company_id' => Company::factory(),
        ];
    }
}
