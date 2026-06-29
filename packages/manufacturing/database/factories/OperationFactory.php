<?php

namespace JeffersonGoncalves\Erp\Manufacturing\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Manufacturing\Models\Operation;

/** @extends Factory<Operation> */
class OperationFactory extends Factory
{
    protected $model = Operation::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true).' Operation',
        ];
    }
}
