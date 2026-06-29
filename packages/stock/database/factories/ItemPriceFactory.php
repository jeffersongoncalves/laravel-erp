<?php

namespace JeffersonGoncalves\Erp\Stock\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Stock\Models\Item;
use JeffersonGoncalves\Erp\Stock\Models\ItemPrice;
use JeffersonGoncalves\Erp\Stock\Models\PriceList;

/** @extends Factory<ItemPrice> */
class ItemPriceFactory extends Factory
{
    protected $model = ItemPrice::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'price_list_id' => PriceList::factory(),
            'rate' => fake()->randomFloat(2, 1, 1000),
            'currency' => 'USD',
        ];
    }
}
