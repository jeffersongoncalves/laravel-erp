<?php

namespace JeffersonGoncalves\Erp\Buying\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Buying\Models\BlanketOrder;
use JeffersonGoncalves\Erp\Buying\Models\BlanketOrderItem;

/** @extends Factory<BlanketOrderItem> */
class BlanketOrderItemFactory extends Factory
{
    protected $model = BlanketOrderItem::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'blanket_order_id' => BlanketOrder::factory(),
            'item_code' => fake()->unique()->bothify('ITEM-####'),
            'qty' => 100,
            'rate' => 10,
            'ordered_qty' => 0,
        ];
    }
}
