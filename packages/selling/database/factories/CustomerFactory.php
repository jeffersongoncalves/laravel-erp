<?php

namespace JeffersonGoncalves\Erp\Selling\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Selling\Models\Customer;

/** @extends Factory<Customer> */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'customer_name' => fake()->unique()->company(),
            'territory' => fake()->country(),
            'customer_type' => 'Company',
            'default_currency' => 'USD',
            'tax_id' => fake()->numerify('##.###.###/####-##'),
            'credit_limit' => 0,
            'disabled' => false,
        ];
    }

    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'disabled' => true,
        ]);
    }
}
