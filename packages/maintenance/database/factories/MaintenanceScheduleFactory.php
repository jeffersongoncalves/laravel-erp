<?php

namespace JeffersonGoncalves\Erp\Maintenance\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Maintenance\Models\MaintenanceSchedule;

/** @extends Factory<MaintenanceSchedule> */
class MaintenanceScheduleFactory extends Factory
{
    protected $model = MaintenanceSchedule::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'party_type' => 'Customer',
            'customer_name' => fake()->company(),
            'transaction_date' => fake()->date(),
            'status' => 'Draft',
            'company_id' => Company::factory(),
        ];
    }
}
