<?php

namespace JeffersonGoncalves\Erp\Core\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\FiscalYear;

/** @extends Factory<FiscalYear> */
class FiscalYearFactory extends Factory
{
    protected $model = FiscalYear::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $year = fake()->unique()->numberBetween(2000, 2100);

        return [
            'name' => (string) $year,
            'year_start_date' => $year.'-01-01',
            'year_end_date' => $year.'-12-31',
            'is_short_year' => false,
        ];
    }
}
