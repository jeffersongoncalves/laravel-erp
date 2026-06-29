<?php

namespace JeffersonGoncalves\Erp\Quality\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Quality\Models\NonConformance;

/** @extends Factory<NonConformance> */
class NonConformanceFactory extends Factory
{
    protected $model = NonConformance::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'subject' => fake()->sentence(),
            'procedure' => fake()->optional()->words(2, true),
            'status' => 'Open',
            'details' => fake()->optional()->paragraph(),
            'company_id' => Company::factory(),
        ];
    }
}
