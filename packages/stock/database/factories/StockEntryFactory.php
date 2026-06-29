<?php

namespace JeffersonGoncalves\Erp\Stock\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Stock\Enums\StockEntryType;
use JeffersonGoncalves\Erp\Stock\Models\StockEntry;

/** @extends Factory<StockEntry> */
class StockEntryFactory extends Factory
{
    protected $model = StockEntry::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'stock_entry_type' => StockEntryType::MaterialReceipt,
            'posting_date' => now(),
            'company_id' => Company::factory(),
        ];
    }

    public function type(StockEntryType $type): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_entry_type' => $type,
        ]);
    }
}
