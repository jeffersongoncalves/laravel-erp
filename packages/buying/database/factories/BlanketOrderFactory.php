<?php

namespace JeffersonGoncalves\Erp\Buying\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Buying\Enums\BlanketOrderType;
use JeffersonGoncalves\Erp\Buying\Models\BlanketOrder;
use JeffersonGoncalves\Erp\Core\Models\Company;

/** @extends Factory<BlanketOrder> */
class BlanketOrderFactory extends Factory
{
    protected $model = BlanketOrder::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'order_type' => BlanketOrderType::Purchasing,
            'party_type' => 'Supplier',
            'from_date' => now(),
            'to_date' => now()->addYear(),
            'company_id' => Company::factory(),
        ];
    }

    public function selling(): static
    {
        return $this->state(fn (array $attributes) => [
            'order_type' => BlanketOrderType::Selling,
            'party_type' => 'Customer',
        ]);
    }
}
