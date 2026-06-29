<?php

namespace JeffersonGoncalves\Erp\Quality\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Quality\Models\QualityInspection;

/** @extends Factory<QualityInspection> */
class QualityInspectionFactory extends Factory
{
    protected $model = QualityInspection::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'item_code' => fake()->optional()->bothify('ITEM-####'),
            'item_name' => fake()->optional()->words(2, true),
            'inspection_type' => 'Incoming',
            'sample_size' => fake()->numberBetween(1, 10),
            'status' => 'Accepted',
            'inspected_by' => fake()->optional()->name(),
            'report_date' => fake()->date(),
            'company_id' => Company::factory(),
        ];
    }
}
