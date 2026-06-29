<?php

namespace JeffersonGoncalves\Erp\Stock\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use JeffersonGoncalves\Erp\Core\Models\Uom;
use JeffersonGoncalves\Erp\Stock\Enums\ValuationMethod;
use JeffersonGoncalves\Erp\Stock\Models\Item;

/** @extends Factory<Item> */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'item_code' => Str::upper(Str::slug($name)).'-'.fake()->unique()->numberBetween(100, 99999),
            'item_name' => Str::title($name),
            'stock_uom_id' => Uom::factory(),
            'is_stock_item' => true,
            'standard_rate' => fake()->randomFloat(2, 1, 500),
            'has_batch_no' => false,
            'has_serial_no' => false,
            'disabled' => false,
        ];
    }

    public function valuation(ValuationMethod $method): static
    {
        return $this->state(fn (array $attributes) => [
            'valuation_method' => $method,
        ]);
    }

    public function nonStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_stock_item' => false,
        ]);
    }
}
