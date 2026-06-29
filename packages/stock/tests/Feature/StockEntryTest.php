<?php

use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Accounting\Services\GeneralLedgerService;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Stock\Enums\StockEntryType;
use JeffersonGoncalves\Erp\Stock\Models\Bin;
use JeffersonGoncalves\Erp\Stock\Models\StockEntry;
use JeffersonGoncalves\Erp\Stock\Models\StockLedgerEntry;
use JeffersonGoncalves\Erp\Stock\Models\Warehouse;

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->stockAccount = Account::factory()->create(['company_id' => $this->company->id]);
    $this->difference = Account::factory()->create(['company_id' => $this->company->id]);
    $this->warehouse = Warehouse::factory()->create([
        'company_id' => $this->company->id,
        'account_id' => $this->stockAccount->id,
    ]);
    $this->item = stockItem();
});

it('receipts stock inbound, fills the bin and posts a balanced ledger', function () {
    $entry = StockEntry::factory()->type(StockEntryType::MaterialReceipt)->create([
        'company_id' => $this->company->id,
        'to_warehouse_id' => $this->warehouse->id,
    ]);

    $entry->items()->create([
        'item_id' => $this->item->id,
        't_warehouse_id' => $this->warehouse->id,
        'qty' => 8,
        'basic_rate' => 12.5,
    ]);

    $entry = $entry->refresh();
    $entry->counterAccountId = $this->difference->id;
    $entry->submit();

    $bin = Bin::query()->first();
    $gl = app(GeneralLedgerService::class);

    expect($entry->docstatus)->toBe(DocStatus::Submitted)
        ->and($bin->actual_qty)->toBe(8.0)
        ->and($bin->valuation_rate)->toBe(12.5)
        ->and($bin->stock_value)->toBe(100.0)
        ->and($gl->accountBalance($this->stockAccount))->toBe(100.0)
        ->and($gl->accountBalance($this->difference))->toBe(-100.0);
});

it('cancels a receipt, emptying the bin and flagging the entry', function () {
    $entry = StockEntry::factory()->type(StockEntryType::MaterialReceipt)->create([
        'company_id' => $this->company->id,
        'to_warehouse_id' => $this->warehouse->id,
    ]);

    $entry->items()->create([
        'item_id' => $this->item->id,
        't_warehouse_id' => $this->warehouse->id,
        'qty' => 8,
        'basic_rate' => 12.5,
    ]);

    $entry = $entry->refresh();
    $entry->counterAccountId = $this->difference->id;
    $entry->submit();
    $entry->cancel();

    $bin = Bin::query()->first();

    expect($bin->actual_qty)->toBe(0.0)
        ->and($bin->stock_value)->toBe(0.0)
        ->and(StockLedgerEntry::query()->where('is_cancelled', false)->count())->toBe(0)
        ->and(app(GeneralLedgerService::class)->accountBalance($this->stockAccount))->toBe(0.0);
});
