<?php

namespace JeffersonGoncalves\Erp\Buying\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Buying\Enums\RfqStatus;
use JeffersonGoncalves\Erp\Buying\Models\RequestForQuotation;
use JeffersonGoncalves\Erp\Core\Models\Company;

/** @extends Factory<RequestForQuotation> */
class RequestForQuotationFactory extends Factory
{
    protected $model = RequestForQuotation::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'transaction_date' => now(),
            'company_id' => Company::factory(),
            'status' => RfqStatus::Draft,
        ];
    }
}
