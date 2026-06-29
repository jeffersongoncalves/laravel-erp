<?php

namespace JeffersonGoncalves\Erp\Selling\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Selling\Models\Quotation;

/** @extends Factory<Quotation> */
class QuotationFactory extends Factory
{
    protected $model = Quotation::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'party_type' => 'Customer',
            'customer_name' => fake()->company(),
            'quotation_date' => fake()->date(),
            'company_id' => Company::factory(),
            'currency' => 'USD',
            'status' => 'Draft',
        ];
    }
}
