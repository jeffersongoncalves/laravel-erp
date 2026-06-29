<?php

use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Stock\Models\Bin;
use JeffersonGoncalves\Erp\Stock\Models\PurchaseReceipt;
use JeffersonGoncalves\Erp\Stock\Models\StockLedgerEntry;
use JeffersonGoncalves\Erp\Stock\Models\StockReconciliation;
use JeffersonGoncalves\Erp\Stock\Models\Warehouse;

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->stockAccount = Account::factory()->create(['company_id' => $this->company->id]);
    $this->srbnb = Account::factory()->create(['company_id' => $this->company->id]);
    $this->difference = Account::factory()->create(['company_id' => $this->company->id]);
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

it('sets the bin to the absolute counted quantity and posts the difference', function () {
    $reconciliation = StockReconciliation::factory()->create([
        'company_id' => $this->company->id,
    ]);
    $reconciliation->items()->create([
        'item_id' => $this->item->id,
        'warehouse_id' => $this->warehouse->id,
        'qty' => 8,
        'valuation_rate' => 5,
    ]);
    $reconciliation = $reconciliation->refresh();
    $reconciliation->counterAccountId = $this->difference->id;
    $reconciliation->submit();

    $bin = Bin::query()->first();
    $delta = StockLedgerEntry::query()->where('actual_qty', '<', 0)->latest('id')->first();

    expect($bin->actual_qty)->toBe(8.0)
        ->and($delta->actual_qty)->toBe(-2.0);
});

it('increases stock when the counted quantity is higher', function () {
    $reconciliation = StockReconciliation::factory()->create([
        'company_id' => $this->company->id,
    ]);
    $reconciliation->items()->create([
        'item_id' => $this->item->id,
        'warehouse_id' => $this->warehouse->id,
        'qty' => 15,
        'valuation_rate' => 5,
    ]);
    $reconciliation = $reconciliation->refresh();
    $reconciliation->counterAccountId = $this->difference->id;
    $reconciliation->submit();

    expect(Bin::query()->first()->actual_qty)->toBe(15.0);
});
