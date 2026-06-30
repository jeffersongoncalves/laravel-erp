<?php

namespace JeffersonGoncalves\Erp\Buying\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Buying\Models\SupplierScorecard;
use JeffersonGoncalves\Erp\Buying\Models\SupplierScorecardCriteria;

/** @extends Factory<SupplierScorecardCriteria> */
class SupplierScorecardCriteriaFactory extends Factory
{
    protected $model = SupplierScorecardCriteria::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'supplier_scorecard_id' => SupplierScorecard::factory(),
            'criteria_name' => fake()->words(2, true),
            'weight' => 50,
            'max_score' => 100,
            'score' => 0,
        ];
    }
}
