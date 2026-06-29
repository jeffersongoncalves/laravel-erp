<?php

use JeffersonGoncalves\Erp\Buying\Enums\PurchaseOrderStatus;
use JeffersonGoncalves\Erp\Buying\Models\PurchaseOrder;
use JeffersonGoncalves\Erp\Buying\Models\SupplierQuotation;
use JeffersonGoncalves\Erp\Buying\Services\SupplierQuotationService;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Core\Models\Company;

beforeEach(function () {
    $this->company = Company::factory()->create();
});

function draftSupplierQuotation(): SupplierQuotation
{
    $quotation = SupplierQuotation::factory()->create([
        'company_id' => test()->company->id,
        'supplier_name' => 'Acme Supplies',
    ]);

    $quotation->items()->create(['item_code' => 'RAW', 'qty' => 4, 'rate' => 25]);
    $quotation->items()->create(['item_code' => 'BOLT', 'qty' => 10, 'rate' => 2]);

    return $quotation->refresh();
}

it('computes totals from its items', function () {
    $quotation = draftSupplierQuotation();

    expect($quotation->net_total)->toBe(120.0)
        ->and($quotation->grand_total)->toBe(120.0);
});

it('can be submitted', function () {
    $quotation = draftSupplierQuotation();

    $quotation->submit();

    expect($quotation->docstatus)->toBe(DocStatus::Submitted)
        ->and($quotation->isSubmitted())->toBeTrue();
});

it('converts into a purchase order copying the items', function () {
    $quotation = draftSupplierQuotation();

    $order = app(SupplierQuotationService::class)->createPurchaseOrder($quotation);

    expect($order)->toBeInstanceOf(PurchaseOrder::class)
        ->and($order->supplier_name)->toBe('Acme Supplies')
        ->and($order->company_id)->toBe($this->company->id)
        ->and($order->status)->toBe(PurchaseOrderStatus::Draft)
        ->and($order->items)->toHaveCount(2)
        ->and($order->net_total)->toBe(120.0)
        ->and($order->items->firstWhere('item_code', 'RAW')->qty)->toBe(4.0)
        ->and($order->items->firstWhere('item_code', 'RAW')->amount)->toBe(100.0);
});
