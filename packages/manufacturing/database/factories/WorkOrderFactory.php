<?php

namespace JeffersonGoncalves\Erp\Manufacturing\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Manufacturing\Enums\WorkOrderStatus;
use JeffersonGoncalves\Erp\Manufacturing\Models\WorkOrder;

/** @extends Factory<WorkOrder> */
class WorkOrderFactory extends Factory
{
    protected $model = WorkOrder::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'production_item' => Str::upper(Str::slug($name)).'-'.fake()->unique()->numberBetween(100, 99999),
            'item_name' => Str::title($name),
            'qty' => fake()->numberBetween(1, 100),
            'company_id' => Company::factory(),
            'status' => WorkOrderStatus::Draft,
        ];
    }
}
