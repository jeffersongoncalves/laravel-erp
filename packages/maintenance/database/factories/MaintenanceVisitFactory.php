<?php

namespace JeffersonGoncalves\Erp\Maintenance\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Maintenance\Models\MaintenanceVisit;

/** @extends Factory<MaintenanceVisit> */
class MaintenanceVisitFactory extends Factory
{
    protected $model = MaintenanceVisit::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'party_type' => 'Customer',
            'customer_name' => fake()->company(),
            'mntc_date' => fake()->date(),
            'maintenance_type' => 'Scheduled',
            'completion_status' => 'Pending',
            'status' => 'Draft',
            'company_id' => Company::factory(),
        ];
    }
}
