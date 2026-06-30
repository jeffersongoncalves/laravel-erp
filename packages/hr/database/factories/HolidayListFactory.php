<?php

namespace JeffersonGoncalves\Erp\Hr\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Hr\Models\HolidayList;

/** @extends Factory<HolidayList> */
class HolidayListFactory extends Factory
{
    protected $model = HolidayList::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'company_id' => Company::factory(),
            'from_date' => fake()->date(),
            'to_date' => fake()->date(),
        ];
    }
}
