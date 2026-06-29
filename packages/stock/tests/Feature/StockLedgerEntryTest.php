<?php

use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Stock\Models\PurchaseReceipt;
use JeffersonGoncalves\Erp\Stock\Models\StockLedgerEntry;
use JeffersonGoncalves\Erp\Stock\Models\Warehouse;

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->stockAccount = Account::factory()->create(['company_id' => $this->company->id]);
    $this->srbnb = Account::factory()->create(['company_id' => $this->company->id]);
    $this->warehouse = Warehouse::factory()->create([
        'company_id' => $this->company->id,
        'account_id' => $this->stockAccount->id,
    ]);
    $this->item = stockItem();

    $receipt = PurchaseReceipt::factory()->create([
        'company_id' => $this->company->id,
        'set_warehouse_id' => $this->warehouse->id,
    ]);
    $receipt->items()->create([
        'item_id' => $this->item->id,
        'qty' => 10,
        'rate' => 5,
        'warehouse_id' => $this->warehouse->id,
    ]);
    $receipt = $receipt->refresh();
    $receipt->counterAccountId = $this->srbnb->id;
    $receipt->submit();
});

it('uses the configured table prefix', function () {
    expect((new StockLedgerEntry)->getTable())->toBe('erp_stock_ledger_entries');
});

it('refuses to mutate the quantity impact of an entry', function () {
    $entry = StockLedgerEntry::query()->first();
    $entry->actual_qty = 999;

    expect(fn () => $entry->save())
        ->toThrow(DomainException::class, 'Stock ledger entries are immutable');
});

it('refuses to delete an entry', function () {
    $entry = StockLedgerEntry::query()->first();

    expect(fn () => $entry->delete())
        ->toThrow(DomainException::class, 'Stock ledger entries cannot be deleted');
});
