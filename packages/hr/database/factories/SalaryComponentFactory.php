<?php

namespace JeffersonGoncalves\Erp\Hr\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Hr\Enums\SalaryComponentType;
use JeffersonGoncalves\Erp\Hr\Models\SalaryComponent;

/** @extends Factory<SalaryComponent> */
class SalaryComponentFactory extends Factory
{
    protected $model = SalaryComponent::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'type' => SalaryComponentType::Earning,
            'account_id' => Account::factory(),
            'is_taxable' => false,
            'amount' => fake()->randomFloat(2, 100, 5000),
        ];
    }

    public function earning(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => SalaryComponentType::Earning,
        ]);
    }

    public function deduction(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => SalaryComponentType::Deduction,
        ]);
    }
}
