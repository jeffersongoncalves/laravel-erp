<?php

use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Stock\Models\DeliveryNote;
use JeffersonGoncalves\Erp\Stock\Models\Warehouse;

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->stockAccount = Account::factory()->create(['company_id' => $this->company->id]);
    $this->cogs = Account::factory()->create(['company_id' => $this->company->id]);
    $this->warehouse = Warehouse::factory()->create([
        'company_id' => $this->company->id,
        'account_id' => $this->stockAccount->id,
    ]);
    $this->item = stockItem();
});

it('refuses an issue that would overdraw the bin when negative stock is disallowed', function () {
    config()->set('erp-stock.allow_negative_stock', false);

    $note = DeliveryNote::factory()->create([
        'company_id' => $this->company->id,
        'set_warehouse_id' => $this->warehouse->id,
    ]);
    $note->items()->create([
        'item_id' => $this->item->id,
        'qty' => 5,
        'rate' => 9,
        'warehouse_id' => $this->warehouse->id,
    ]);
    $note = $note->refresh();
    $note->counterAccountId = $this->cogs->id;

    expect(fn () => $note->submit())
        ->toThrow(DomainException::class, 'Negative stock');
});

it('allows the issue when negative stock is permitted', function () {
    config()->set('erp-stock.allow_negative_stock', true);

    $note = DeliveryNote::factory()->create([
        'company_id' => $this->company->id,
        'set_warehouse_id' => $this->warehouse->id,
    ]);
    $note->items()->create([
        'item_id' => $this->item->id,
        'qty' => 5,
        'rate' => 9,
        'warehouse_id' => $this->warehouse->id,
    ]);
    $note = $note->refresh();
    $note->counterAccountId = $this->cogs->id;
    $note->submit();

    expect($note->isSubmitted())->toBeTrue();
});
