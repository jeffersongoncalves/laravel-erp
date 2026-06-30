<?php

namespace JeffersonGoncalves\Erp\Stock\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Stock\Models\DeliveryNote;
use JeffersonGoncalves\Erp\Stock\Models\PackingSlip;

/** @extends Factory<PackingSlip> */
class PackingSlipFactory extends Factory
{
    protected $model = PackingSlip::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'delivery_note_id' => DeliveryNote::factory(),
            'from_case_no' => 1,
            'to_case_no' => 1,
            'net_weight' => fake()->randomFloat(2, 1, 100),
            'gross_weight' => fake()->randomFloat(2, 1, 100),
        ];
    }
}
