<?php

namespace JeffersonGoncalves\Erp\Stock\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Stock\Models\Shipment;
use JeffersonGoncalves\Erp\Stock\Models\ShipmentParcel;

/** @extends Factory<ShipmentParcel> */
class ShipmentParcelFactory extends Factory
{
    protected $model = ShipmentParcel::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'shipment_id' => Shipment::factory(),
            'length' => fake()->randomFloat(2, 1, 100),
            'width' => fake()->randomFloat(2, 1, 100),
            'height' => fake()->randomFloat(2, 1, 100),
            'weight' => fake()->randomFloat(2, 1, 100),
            'count' => 1,
        ];
    }
}
