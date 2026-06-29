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
    $this->item = stockItem([], ValuationMethod::FIFO);
});

function fifoReceipt(float $qty, float $rate): void
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

it('consumes the oldest layer first', function () {
    fifoReceipt(10, 10);
    fifoReceipt(10, 20);

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

    $outbound = StockLedgerEntry::query()->where('actual_qty', '<', 0)->latest('id')->first();
    $bin = Bin::query()->first();

    // 5 units consumed entirely from the first layer at rate 10.
    expect($outbound->valuation_rate)->toBe(10.0)
        ->and($bin->actual_qty)->toBe(15.0)
        ->and($bin->stock_value)->toBe(250.0);
});

it('crosses layer boundaries at a blended rate', function () {
    fifoReceipt(10, 10);
    fifoReceipt(10, 20);

    $issue = StockEntry::factory()->type(StockEntryType::MaterialIssue)->create([
        'company_id' => $this->company->id,
        'from_warehouse_id' => $this->warehouse->id,
    ]);
    $issue->items()->create([
        'item_id' => $this->item->id,
        's_warehouse_id' => $this->warehouse->id,
        'qty' => 15,
    ]);
    $issue->refresh()->submit();

    $outbound = StockLedgerEntry::query()->where('actual_qty', '<', 0)->latest('id')->first();

    // 10 @ 10 + 5 @ 20 = 200 over 15 units = 13.333...
    expect(round((float) $outbound->valuation_rate, 4))->toBe(13.3333)
        ->and(Bin::query()->first()->actual_qty)->toBe(5.0);
});
