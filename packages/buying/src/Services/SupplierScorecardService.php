<?php

namespace JeffersonGoncalves\Erp\Buying\Services;

use JeffersonGoncalves\Erp\Buying\Models\SupplierScorecard;

/**
 * Computes the weighted score and standing of a supplier scorecard.
 */
class SupplierScorecardService
{
    /**
     * The weighted score (0..100) summed across the card's criteria.
     *
     * Each criterion contributes (score / max_score) * weight, where weights
     * are percentages summing to roughly 100.
     */
    public function computeScore(SupplierScorecard $card): float
    {
        $total = 0.0;

        foreach ($card->criteria as $criteria) {
            if ((float) $criteria->max_score <= 0.0) {
                continue;
            }

            $total += ((float) $criteria->score / (float) $criteria->max_score) * (float) $criteria->weight;
        }

        return $total;
    }

    /**
     * Map a weighted score to a standing grade.
     */
    public function grade(float $score): string
    {
        return match (true) {
            $score >= 90 => 'A',
            $score >= 75 => 'B',
            $score >= 50 => 'C',
            default => 'D',
        };
    }

    /**
     * Recompute and persist the card's score and standing.
     */
    public function refresh(SupplierScorecard $card): void
    {
        $score = $this->computeScore($card);

        $card->score = $score;
        $card->standing = $this->grade($score);
        $card->save();
    }
}
