<?php

namespace JeffersonGoncalves\Erp\Quality\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Quality\Models\QualityInspectionTemplate;

/** @extends Factory<QualityInspectionTemplate> */
class QualityInspectionTemplateFactory extends Factory
{
    protected $model = QualityInspectionTemplate::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->unique()->words(3, true)),
        ];
    }
}
