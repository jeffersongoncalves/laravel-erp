<?php

namespace JeffersonGoncalves\Erp\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Crm\Models\Appointment;

/** @extends Factory<Appointment> */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'scheduled_time' => fake()->dateTimeBetween('now', '+1 month'),
            'status' => 'Open',
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => fake()->phoneNumber(),
            'company_id' => Company::factory(),
        ];
    }
}
