<?php

namespace JeffersonGoncalves\Erp\Core\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\TermsAndConditions;

/** @extends Factory<TermsAndConditions> */
class TermsAndConditionsFactory extends Factory
{
    protected $model = TermsAndConditions::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->sentence(3),
            'content' => fake()->paragraphs(3, true),
        ];
    }
}
