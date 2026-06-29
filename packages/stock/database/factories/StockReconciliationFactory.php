<?php

namespace JeffersonGoncalves\Erp\Stock\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Stock\Models\StockReconciliation;

/** @extends Factory<StockReconciliation> */
class StockReconciliationFactory extends Factory
{
    protected $model = StockReconciliation::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'posting_date' => now(),
            'company_id' => Company::factory(),
        ];
    }
}
