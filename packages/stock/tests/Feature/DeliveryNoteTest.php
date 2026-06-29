<?php

use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Accounting\Services\GeneralLedgerService;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Stock\Models\Bin;
use JeffersonGoncalves\Erp\Stock\Models\DeliveryNote;
use JeffersonGoncalves\Erp\Stock\Models\PurchaseReceipt;
use JeffersonGoncalves\Erp\Stock\Models\Warehouse;

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->stockAccount = Account::factory()->create(['company_id' => $this->company->id]);
    $this->srbnb = Account::factory()->create(['company_id' => $this->company->id]);
    $this->cogs = Account::factory()->create(['company_id' => $this->company->id]);
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

it('ships stock outbound, reducing the bin and posting cost of goods sold', function () {
    $note = DeliveryNote::factory()->create([
        'company_id' => $this->company->id,
        'set_warehouse_id' => $this->warehouse->id,
    ]);

    $note->items()->create([
        'item_id' => $this->item->id,
        'qty' => 3,
        'rate' => 9,
        'warehouse_id' => $this->warehouse->id,
    ]);

    $note = $note->refresh();
    $note->counterAccountId = $this->cogs->id;
    $note->submit();

    $bin = Bin::query()->first();
    $gl = app(GeneralLedgerService::class);

    expect($bin->actual_qty)->toBe(7.0)
        ->and($bin->stock_value)->toBe(35.0)
        ->and($gl->accountBalance($this->cogs))->toBe(15.0)
        ->and($gl->accountBalance($this->stockAccount))->toBe(35.0);
});
