<?php

use JeffersonGoncalves\Erp\Buying\Enums\BlanketOrderType;
use JeffersonGoncalves\Erp\Buying\Models\BlanketOrder;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Core\Models\Company;

beforeEach(function () {
    $this->company = Company::factory()->create();
});

it('defaults to a purchasing draft', function () {
    $order = BlanketOrder::factory()->create(['company_id' => $this->company->id]);

    expect($order->order_type)->toBe(BlanketOrderType::Purchasing)
        ->and($order->party_type)->toBe('Supplier')
        ->and($order->docstatus)->toBe(DocStatus::Draft);
});

it('has many items', function () {
    $order = BlanketOrder::factory()->create(['company_id' => $this->company->id]);
    $order->items()->create(['item_code' => 'RAW', 'qty' => 100, 'rate' => 5]);

    expect($order->refresh()->items)->toHaveCount(1)
        ->and($order->items->first()->qty)->toBe(100.0);
});

it('can be submitted and becomes immutable without posting a ledger', function () {
    $order = BlanketOrder::factory()->create(['company_id' => $this->company->id]);

    $order->submit();

    expect($order->docstatus)->toBe(DocStatus::Submitted);

    $order->party_type = 'Customer';
    expect(fn () => $order->save())->toThrow(DomainException::class);
});
