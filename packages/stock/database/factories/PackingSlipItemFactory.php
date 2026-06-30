<?php

namespace JeffersonGoncalves\Erp\Stock\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Stock\Models\Item;
use JeffersonGoncalves\Erp\Stock\Models\PackingSlip;
use JeffersonGoncalves\Erp\Stock\Models\PackingSlipItem;

/** @extends Factory<PackingSlipItem> */
class PackingSlipItemFactory extends Factory
{
    protected $model = PackingSlipItem::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'packing_slip_id' => PackingSlip::factory(),
            'item_id' => Item::factory(),
            'qty' => fake()->randomFloat(2, 1, 100),
        ];
    }
}
