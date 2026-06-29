<?php

namespace JeffersonGoncalves\Erp\Buying\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Buying\Models\SupplierQuotation;
use JeffersonGoncalves\Erp\Core\Models\Company;

/** @extends Factory<SupplierQuotation> */
class SupplierQuotationFactory extends Factory
{
    protected $model = SupplierQuotation::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'party_type' => 'Supplier',
            'supplier_name' => fake()->company(),
            'transaction_date' => now(),
            'company_id' => Company::factory(),
            'currency' => 'USD',
        ];
    }
}
