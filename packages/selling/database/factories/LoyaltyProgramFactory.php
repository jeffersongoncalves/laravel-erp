<?php

namespace JeffersonGoncalves\Erp\Selling\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Selling\Models\LoyaltyProgram;

/** @extends Factory<LoyaltyProgram> */
class LoyaltyProgramFactory extends Factory
{
    protected $model = LoyaltyProgram::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'loyalty_program_name' => fake()->unique()->words(2, true),
            'company_id' => Company::factory(),
            'conversion_factor' => 1,
            'expiry_duration' => 0,
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
