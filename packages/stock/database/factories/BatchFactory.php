<?php

namespace JeffersonGoncalves\Erp\Stock\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Stock\Models\Batch;
use JeffersonGoncalves\Erp\Stock\Models\Item;

/** @extends Factory<Batch> */
class BatchFactory extends Factory
{
    protected $model = Batch::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'batch_id' => 'BATCH-'.fake()->unique()->numberBetween(10000, 99999),
            'item_id' => Item::factory(),
            'batch_qty' => 0,
        ];
    }
}
