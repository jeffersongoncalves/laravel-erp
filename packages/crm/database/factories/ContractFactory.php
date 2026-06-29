<?php

namespace JeffersonGoncalves\Erp\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Crm\Models\Contract;

/** @extends Factory<Contract> */
class ContractFactory extends Factory
{
    protected $model = Contract::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'party_type' => 'Customer',
            'party_name' => fake()->company(),
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
            'status' => 'Unsigned',
            'is_signed' => false,
            'company_id' => Company::factory(),
        ];
    }

    public function signed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Active',
            'is_signed' => true,
        ]);
    }
}
