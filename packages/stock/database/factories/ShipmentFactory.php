<?php

namespace JeffersonGoncalves\Erp\Stock\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Stock\Models\Shipment;

/** @extends Factory<Shipment> */
class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'pickup_from_type' => 'Company',
            'delivery_to_type' => 'Customer',
            'shipment_date' => now(),
            'value_of_goods' => fake()->randomFloat(2, 1, 1000),
        ];
    }
}
