<?php

namespace JeffersonGoncalves\Erp\Hr\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Hr\Enums\EmployeeStatus;
use JeffersonGoncalves\Erp\Hr\Enums\Gender;
use JeffersonGoncalves\Erp\Hr\Models\Employee;

/** @extends Factory<Employee> */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'employee_number' => 'EMP-'.fake()->unique()->numberBetween(1000, 99999),
            'company_id' => Company::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'gender' => fake()->randomElement(Gender::cases()),
            'date_of_birth' => fake()->date(),
            'date_of_joining' => fake()->date(),
            'status' => EmployeeStatus::Active,
            'ctc' => fake()->randomFloat(2, 30000, 200000),
        ];
    }

    public function left(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EmployeeStatus::Left,
            'date_of_leaving' => fake()->date(),
        ]);
    }
}
