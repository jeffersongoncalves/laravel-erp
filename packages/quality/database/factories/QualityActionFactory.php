<?php

namespace JeffersonGoncalves\Erp\Quality\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Quality\Models\QualityAction;

/** @extends Factory<QualityAction> */
class QualityActionFactory extends Factory
{
    protected $model = QualityAction::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'corrective_preventive' => fake()->randomElement(['Corrective', 'Preventive']),
            'status' => 'Open',
            'review' => fake()->optional()->words(2, true),
            'date' => fake()->date(),
            'goal' => fake()->optional()->words(2, true),
            'procedure' => fake()->optional()->words(2, true),
            'company_id' => Company::factory(),
        ];
    }
}
