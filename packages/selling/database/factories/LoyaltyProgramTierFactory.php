<?php

namespace JeffersonGoncalves\Erp\Selling\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Selling\Models\LoyaltyProgram;
use JeffersonGoncalves\Erp\Selling\Models\LoyaltyProgramTier;

/** @extends Factory<LoyaltyProgramTier> */
class LoyaltyProgramTierFactory extends Factory
{
    protected $model = LoyaltyProgramTier::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'loyalty_program_id' => LoyaltyProgram::factory(),
            'tier_name' => fake()->unique()->word(),
            'min_spent' => 0,
            'collection_factor' => 1,
        ];
    }
}
