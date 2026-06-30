<?php

namespace JeffersonGoncalves\Erp\Hr\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Hr\Enums\AttendanceStatus;
use JeffersonGoncalves\Erp\Hr\Models\Attendance;
use JeffersonGoncalves\Erp\Hr\Models\Employee;

/** @extends Factory<Attendance> */
class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'company_id' => Company::factory(),
            'attendance_date' => fake()->date(),
            'status' => AttendanceStatus::Present,
        ];
    }
}
