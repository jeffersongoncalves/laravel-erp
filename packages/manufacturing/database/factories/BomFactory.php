<?php

namespace JeffersonGoncalves\Erp\Manufacturing\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Manufacturing\Models\Bom;

/** @extends Factory<Bom> */
class BomFactory extends Factory
{
    protected $model = Bom::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'item_code' => Str::upper(Str::slug($name)).'-'.fake()->unique()->numberBetween(100, 99999),
            'item_name' => Str::title($name),
            'quantity' => 1,
            'company_id' => Company::factory(),
            'is_active' => true,
            'is_default' => false,
            'with_operations' => false,
            'currency' => 'USD',
        ];
    }
}
