<?php

namespace JeffersonGoncalves\Erp\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Crm\Models\LeadSource;

/** @extends Factory<LeadSource> */
class LeadSourceFactory extends Factory
{
    protected $model = LeadSource::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Advertisement', 'Customer Reference', 'Cold Calling',
                'Exhibition', 'Supplier Reference', 'Mass Mailing',
                'Existing Customer', 'Walk In', 'Campaign',
            ]).' '.fake()->unique()->numberBetween(1, 9999),
            'details' => fake()->optional()->sentence(),
        ];
    }
}
