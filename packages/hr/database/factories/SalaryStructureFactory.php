<?php

namespace JeffersonGoncalves\Erp\Hr\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Hr\Models\SalaryStructure;

/** @extends Factory<SalaryStructure> */
class SalaryStructureFactory extends Factory
{
    protected $model = SalaryStructure::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'company_id' => Company::factory(),
            'is_active' => true,
        ];
    }
}
