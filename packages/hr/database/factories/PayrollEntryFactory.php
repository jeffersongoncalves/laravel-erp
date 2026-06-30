<?php

namespace JeffersonGoncalves\Erp\Hr\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Hr\Enums\PayrollFrequency;
use JeffersonGoncalves\Erp\Hr\Models\PayrollEntry;

/** @extends Factory<PayrollEntry> */
class PayrollEntryFactory extends Factory
{
    protected $model = PayrollEntry::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
            'payroll_frequency' => PayrollFrequency::Monthly,
        ];
    }
}
