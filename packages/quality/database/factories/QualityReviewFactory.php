<?php

namespace JeffersonGoncalves\Erp\Quality\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Quality\Models\QualityReview;

/** @extends Factory<QualityReview> */
class QualityReviewFactory extends Factory
{
    protected $model = QualityReview::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'goal' => fake()->optional()->words(2, true),
            'date' => fake()->date(),
            'status' => 'Open',
            'company_id' => Company::factory(),
        ];
    }
}
