<?php

namespace JeffersonGoncalves\Erp\Hr\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Hr\Models\Employee;
use JeffersonGoncalves\Erp\Hr\Models\SalarySlip;

/** @extends Factory<SalarySlip> */
class SalarySlipFactory extends Factory
{
    protected $model = SalarySlip::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'company_id' => Company::factory(),
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
            'posting_date' => fake()->date(),
        ];
    }
}
