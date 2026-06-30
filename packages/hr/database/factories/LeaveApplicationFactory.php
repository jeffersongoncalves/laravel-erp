<?php

namespace JeffersonGoncalves\Erp\Hr\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Hr\Enums\LeaveApplicationStatus;
use JeffersonGoncalves\Erp\Hr\Models\Employee;
use JeffersonGoncalves\Erp\Hr\Models\LeaveApplication;
use JeffersonGoncalves\Erp\Hr\Models\LeaveType;

/** @extends Factory<LeaveApplication> */
class LeaveApplicationFactory extends Factory
{
    protected $model = LeaveApplication::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'leave_type_id' => LeaveType::factory(),
            'from_date' => fake()->date(),
            'to_date' => fake()->date(),
            'total_leave_days' => fake()->randomFloat(2, 1, 5),
            'status' => LeaveApplicationStatus::Open,
        ];
    }
}
