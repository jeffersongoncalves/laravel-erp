<?php

namespace JeffersonGoncalves\Erp\Stock\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Stock\Enums\SerialNoStatus;
use JeffersonGoncalves\Erp\Stock\Models\Item;
use JeffersonGoncalves\Erp\Stock\Models\SerialNo;

/** @extends Factory<SerialNo> */
class SerialNoFactory extends Factory
{
    protected $model = SerialNo::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'serial_no' => 'SN-'.fake()->unique()->numberBetween(100000, 999999),
            'item_id' => Item::factory(),
            'status' => SerialNoStatus::Active,
            'purchase_rate' => fake()->randomFloat(2, 1, 500),
        ];
    }
}
