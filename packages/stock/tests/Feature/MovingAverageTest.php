<?php

use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Stock\Enums\StockEntryType;
use JeffersonGoncalves\Erp\Stock\Enums\ValuationMethod;
use JeffersonGoncalves\Erp\Stock\Models\Bin;
use JeffersonGoncalves\Erp\Stock\Models\PurchaseReceipt;
use JeffersonGoncalves\Erp\Stock\Models\StockEntry;
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
    $this->item = stockItem([], ValuationMethod::MovingAverage);
});

function maReceipt(float $qty, float $rate): void
{
    $receipt = PurchaseReceipt::factory()->create([
        'company_id' => test()->company->id,
        'set_warehouse_id' => test()->warehouse->id,
    ]);
    $receipt->items()->create([
        'item_id' => test()->item->id,
        'qty' => $qty,
        'rate' => $rate,
        'warehouse_id' => test()->warehouse->id,
    ]);
    $receipt = $receipt->refresh();
    $receipt->counterAccountId = test()->srbnb->id;
    $receipt->submit();
}

it('values the bin at the weighted average of two receipts', function () {
    maReceipt(10, 10);
    maReceipt(10, 20);

    $bin = Bin::query()->first();

    expect($bin->actual_qty)->toBe(20.0)
        ->and($bin->valuation_rate)->toBe(15.0)
        ->and($bin->stock_value)->toBe(300.0);
});

it('consumes an issue at the moving average rate', function () {
    maReceipt(10, 10);
    maReceipt(10, 20);

    $issue = StockEntry::factory()->type(StockEntryType::MaterialIssue)->create([
        'company_id' => $this->company->id,
        'from_warehouse_id' => $this->warehouse->id,
    ]);
    $issue->items()->create([
        'item_id' => $this->item->id,
        's_warehouse_id' => $this->warehouse->id,
        'qty' => 5,
    ]);
    $issue->refresh()->submit();

    $bin = Bin::query()->first();
    $outbound = StockLedgerEntry::query()->where('actual_qty', '<', 0)->latest('id')->first();

    expect($outbound->valuation_rate)->toBe(15.0)
        ->and($bin->actual_qty)->toBe(15.0)
        ->and($bin->stock_value)->toBe(225.0)
        ->and($bin->valuation_rate)->toBe(15.0);
});
