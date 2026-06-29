<?php

use JeffersonGoncalves\Erp\Buying\Enums\PurchaseOrderStatus;
use JeffersonGoncalves\Erp\Buying\Models\PurchaseOrder;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Core\Models\Company;

beforeEach(function () {
    $this->company = Company::factory()->create();
});

function draftPurchaseOrder(): PurchaseOrder
{
    $order = PurchaseOrder::factory()->create([
        'company_id' => test()->company->id,
        'supplier_name' => 'Acme Supplies',
    ]);

    $order->items()->create(['item_code' => 'RAW', 'qty' => 4, 'rate' => 25]);
    $order->items()->create(['item_code' => 'BOLT', 'qty' => 10, 'rate' => 2]);

    return $order->refresh();
}

it('defaults to a draft status', function () {
    $order = PurchaseOrder::factory()->create(['company_id' => $this->company->id]);

    expect($order->status)->toBe(PurchaseOrderStatus::Draft)
        ->and($order->per_received)->toBe(0.0)
        ->and($order->per_billed)->toBe(0.0)
        ->and($order->docstatus)->toBe(DocStatus::Draft);
});

it('computes totals from its items', function () {
    $order = draftPurchaseOrder();

    expect($order->net_total)->toBe(120.0)
        ->and($order->grand_total)->toBe(120.0)
        ->and($order->items->firstWhere('item_code', 'BOLT')->amount)->toBe(20.0);
});

it('can be submitted and becomes immutable', function () {
    $order = draftPurchaseOrder();

    $order->submit();

    expect($order->docstatus)->toBe(DocStatus::Submitted);

    $order->status = PurchaseOrderStatus::Completed;
    expect(fn () => $order->save())->toThrow(DomainException::class);
});

it('tracks the order status as it progresses', function () {
    $order = draftPurchaseOrder();

    $order->status = PurchaseOrderStatus::ToReceiveAndBill;
    $order->save();

    expect($order->refresh()->status)->toBe(PurchaseOrderStatus::ToReceiveAndBill);
});
