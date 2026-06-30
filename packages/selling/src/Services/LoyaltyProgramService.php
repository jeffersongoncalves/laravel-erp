<?php

namespace JeffersonGoncalves\Erp\Selling\Services;

use JeffersonGoncalves\Erp\Selling\Models\LoyaltyProgram;
use JeffersonGoncalves\Erp\Selling\Models\LoyaltyProgramTier;

/**
 * Resolves the loyalty tier a spend qualifies for and awards points for a
 * purchase amount. Pure computation: no entries are persisted here.
 */
class LoyaltyProgramService
{
    /**
     * The highest tier whose minimum spend is at or below $spent, or null when
     * the spend does not reach any tier.
     */
    public function tierFor(LoyaltyProgram $program, float $spent): ?LoyaltyProgramTier
    {
        return LoyaltyProgramTier::query()
            ->where('loyalty_program_id', $program->getKey())
            ->where('min_spent', '<=', $spent)
            ->orderByDesc('min_spent')
            ->first();
    }

    /**
     * Points earned for a purchase amount: floor(amount * tier collection factor).
     * Returns zero when the amount qualifies for no tier.
     */
    public function awardPoints(LoyaltyProgram $program, float $amount): int
    {
        $tier = $this->tierFor($program, $amount);

        if ($tier === null) {
            return 0;
        }

        return (int) floor($amount * (float) $tier->collection_factor);
    }
}
