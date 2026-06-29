<?php

namespace JeffersonGoncalves\Erp\Projects\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Projects\Models\Timesheet;

/** @extends Factory<Timesheet> */
class TimesheetFactory extends Factory
{
    protected $model = Timesheet::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'employee' => fake()->name(),
            'user' => fake()->safeEmail(),
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
            'status' => 'Draft',
            'company_id' => Company::factory(),
        ];
    }
}
