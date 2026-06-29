<?php

namespace JeffersonGoncalves\Erp\Stock\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Stock\Models\PurchaseReceipt;

/** @extends Factory<PurchaseReceipt> */
class PurchaseReceiptFactory extends Factory
{
    protected $model = PurchaseReceipt::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'party_type' => 'Supplier',
            'supplier_name' => fake()->company(),
            'posting_date' => now(),
            'company_id' => Company::factory(),
        ];
    }
}
