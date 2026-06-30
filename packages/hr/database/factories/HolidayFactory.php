<?php

namespace JeffersonGoncalves\Erp\Hr\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Hr\Models\Holiday;
use JeffersonGoncalves\Erp\Hr\Models\HolidayList;

/** @extends Factory<Holiday> */
class HolidayFactory extends Factory
{
    protected $model = Holiday::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'holiday_list_id' => HolidayList::factory(),
            'holiday_date' => fake()->date(),
            'description' => fake()->sentence(3),
        ];
    }
}
