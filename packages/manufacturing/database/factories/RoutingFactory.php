<?php

namespace JeffersonGoncalves\Erp\Manufacturing\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use JeffersonGoncalves\Erp\Manufacturing\Models\Routing;

/** @extends Factory<Routing> */
class RoutingFactory extends Factory
{
    protected $model = Routing::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => Str::title(fake()->unique()->words(2, true)).' Routing',
            'disabled' => false,
        ];
    }
}
