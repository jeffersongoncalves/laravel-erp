<?php

use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Accounting\Services\GeneralLedgerService;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Stock\Models\Bin;
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
});

function submittedReceipt(float $qty, float $rate): PurchaseReceipt
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

    return $receipt;
}

it('creates an inbound stock ledger entry and updates the bin', function () {
    submittedReceipt(10, 5);

    $sle = StockLedgerEntry::query()->first();
    $bin = Bin::query()->first();

    expect($sle->actual_qty)->toBe(10.0)
        ->and($sle->qty_after_transaction)->toBe(10.0)
        ->and($sle->valuation_rate)->toBe(5.0)
        ->and($sle->stock_value)->toBe(50.0)
        ->and($bin->actual_qty)->toBe(10.0)
        ->and($bin->valuation_rate)->toBe(5.0)
        ->and($bin->stock_value)->toBe(50.0);
});

it('posts a balanced ledger debiting stock and crediting stock received but not billed', function () {
    submittedReceipt(10, 5);

    $gl = app(GeneralLedgerService::class);

    expect($gl->accountBalance($this->stockAccount))->toBe(50.0)
        ->and($gl->accountBalance($this->srbnb))->toBe(-50.0);
});

it('reverses the bin and ledger on cancel', function () {
    $receipt = submittedReceipt(10, 5);
    $receipt->cancel();

    $gl = app(GeneralLedgerService::class);
    $bin = Bin::query()->first();

    expect($receipt->docstatus)->toBe(DocStatus::Cancelled)
        ->and($bin->actual_qty)->toBe(0.0)
        ->and($bin->stock_value)->toBe(0.0)
        ->and(StockLedgerEntry::query()->where('is_cancelled', false)->count())->toBe(0)
        ->and($gl->accountBalance($this->stockAccount))->toBe(0.0)
        ->and($gl->accountBalance($this->srbnb))->toBe(0.0);
});
