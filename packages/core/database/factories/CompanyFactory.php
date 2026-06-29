<?php

namespace JeffersonGoncalves\Erp\Core\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use JeffersonGoncalves\Erp\Core\Models\Company;

/** @extends Factory<Company> */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'name' => $name,
            'abbr' => Str::upper(Str::substr(Str::slug($name), 0, 5)).fake()->unique()->numberBetween(100, 999),
            'default_currency' => 'USD',
            'country' => fake()->country(),
            'tax_id' => fake()->numerify('##.###.###/####-##'),
            'is_group' => false,
        ];
    }

    public function group(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_group' => true,
        ]);
    }
}
