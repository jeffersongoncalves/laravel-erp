<?php

namespace JeffersonGoncalves\Erp\Hr\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Hr\Enums\SalaryComponentType;
use JeffersonGoncalves\Erp\Hr\Models\SalaryComponent;
use JeffersonGoncalves\Erp\Hr\Models\SalarySlip;
use JeffersonGoncalves\Erp\Hr\Models\SalarySlipComponent;

/** @extends Factory<SalarySlipComponent> */
class SalarySlipComponentFactory extends Factory
{
    protected $model = SalarySlipComponent::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'salary_slip_id' => SalarySlip::factory(),
            'salary_component_id' => SalaryComponent::factory(),
            'type' => SalaryComponentType::Earning,
            'amount' => fake()->randomFloat(2, 100, 5000),
        ];
    }
}
