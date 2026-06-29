<?php

namespace JeffersonGoncalves\Erp\Manufacturing\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Manufacturing\Enums\JobCardStatus;
use JeffersonGoncalves\Erp\Manufacturing\Models\JobCard;
use JeffersonGoncalves\Erp\Manufacturing\Models\WorkOrder;

/** @extends Factory<JobCard> */
class JobCardFactory extends Factory
{
    protected $model = JobCard::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'work_order_id' => WorkOrder::factory(),
            'for_quantity' => fake()->numberBetween(1, 50),
            'status' => JobCardStatus::Open,
            'company_id' => Company::factory(),
        ];
    }
}
