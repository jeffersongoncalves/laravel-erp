<?php

namespace JeffersonGoncalves\Erp\Stock\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Stock\Models\DeliveryNote;

/** @extends Factory<DeliveryNote> */
class DeliveryNoteFactory extends Factory
{
    protected $model = DeliveryNote::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'party_type' => 'Customer',
            'customer_name' => fake()->name(),
            'posting_date' => now(),
            'company_id' => Company::factory(),
        ];
    }
}
