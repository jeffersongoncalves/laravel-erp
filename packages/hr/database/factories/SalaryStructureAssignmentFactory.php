<?php

namespace JeffersonGoncalves\Erp\Hr\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Hr\Models\Employee;
use JeffersonGoncalves\Erp\Hr\Models\SalaryStructure;
use JeffersonGoncalves\Erp\Hr\Models\SalaryStructureAssignment;

/** @extends Factory<SalaryStructureAssignment> */
class SalaryStructureAssignmentFactory extends Factory
{
    protected $model = SalaryStructureAssignment::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'salary_structure_id' => SalaryStructure::factory(),
            'from_date' => fake()->date(),
            'base' => fake()->randomFloat(2, 1000, 10000),
        ];
    }
}
