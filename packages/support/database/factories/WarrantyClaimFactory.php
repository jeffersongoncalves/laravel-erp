<?php

namespace JeffersonGoncalves\Erp\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Support\Models\WarrantyClaim;

/** @extends Factory<WarrantyClaim> */
class WarrantyClaimFactory extends Factory
{
    protected $model = WarrantyClaim::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'party_type' => 'Customer',
            'customer_name' => fake()->optional()->company(),
            'serial_no' => fake()->optional()->bothify('SN-####-????'),
            'item_code' => fake()->optional()->bothify('ITEM-####'),
            'complaint' => fake()->optional()->sentence(),
            'status' => 'Open',
            'complaint_date' => fake()->date(),
            'company_id' => Company::factory(),
        ];
    }
}
