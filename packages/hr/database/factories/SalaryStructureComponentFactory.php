<?php

namespace JeffersonGoncalves\Erp\Hr\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Hr\Models\SalaryComponent;
use JeffersonGoncalves\Erp\Hr\Models\SalaryStructure;
use JeffersonGoncalves\Erp\Hr\Models\SalaryStructureComponent;

/** @extends Factory<SalaryStructureComponent> */
class SalaryStructureComponentFactory extends Factory
{
    protected $model = SalaryStructureComponent::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'salary_structure_id' => SalaryStructure::factory(),
            'salary_component_id' => SalaryComponent::factory(),
            'amount' => fake()->randomFloat(2, 100, 5000),
        ];
    }
}
