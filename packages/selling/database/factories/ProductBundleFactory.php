<?php

namespace JeffersonGoncalves\Erp\Selling\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use JeffersonGoncalves\Erp\Selling\Models\ProductBundle;

/** @extends Factory<ProductBundle> */
class ProductBundleFactory extends Factory
{
    protected $model = ProductBundle::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'new_item_code' => Str::upper(Str::slug($name)).'-'.fake()->unique()->numberBetween(100, 99999),
            'description' => fake()->sentence(),
            'disabled' => false,
        ];
    }
}
