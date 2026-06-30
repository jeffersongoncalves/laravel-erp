<?php

namespace JeffersonGoncalves\Erp\Selling\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Selling\Models\Customer;
use JeffersonGoncalves\Erp\Selling\Models\LoyaltyPointEntry;
use JeffersonGoncalves\Erp\Selling\Models\LoyaltyProgram;

/** @extends Factory<LoyaltyPointEntry> */
class LoyaltyPointEntryFactory extends Factory
{
    protected $model = LoyaltyPointEntry::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'loyalty_program_id' => LoyaltyProgram::factory(),
            'party_type' => 'Customer',
            'party_id' => Customer::factory(),
            'posting_date' => fake()->date(),
            'purchase_amount' => 0,
            'loyalty_points' => 0,
        ];
    }
}
