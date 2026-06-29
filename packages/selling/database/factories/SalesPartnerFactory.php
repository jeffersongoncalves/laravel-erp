<?php

namespace JeffersonGoncalves\Erp\Selling\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Selling\Models\SalesPartner;

/** @extends Factory<SalesPartner> */
class SalesPartnerFactory extends Factory
{
    protected $model = SalesPartner::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'commission_rate' => fake()->randomFloat(2, 0, 20),
            'partner_type' => fake()->randomElement(['Channel Partner', 'Distributor', 'Reseller']),
        ];
    }
}
