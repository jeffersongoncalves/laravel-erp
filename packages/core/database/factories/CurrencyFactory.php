<?php

namespace JeffersonGoncalves\Erp\Core\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use JeffersonGoncalves\Erp\Core\Models\Currency;

/** @extends Factory<Currency> */
class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'code' => Str::upper(fake()->unique()->lexify('???')),
            'name' => fake()->words(2, true),
            'symbol' => fake()->randomElement(['$', '€', '£', 'R$']),
            'fraction' => 'Cent',
            'fraction_units' => 100,
            'number_format' => '#,###.##',
            'enabled' => true,
        ];
    }

    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'enabled' => false,
        ]);
    }
}
