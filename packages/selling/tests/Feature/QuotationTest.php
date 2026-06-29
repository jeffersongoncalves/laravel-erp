<?php

use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Selling\Enums\QuotationStatus;
use JeffersonGoncalves\Erp\Selling\Models\Quotation;
use JeffersonGoncalves\Erp\Selling\Models\SalesOrder;
use JeffersonGoncalves\Erp\Selling\Services\QuotationService;

it('recomputes totals from its items while draft', function () {
    $quotation = Quotation::factory()->create();

    $quotation->items()->create(['item_code' => 'A', 'qty' => 2, 'rate' => 50]);
    $quotation->items()->create(['item_code' => 'B', 'qty' => 1, 'rate' => 30]);

    $quotation->refresh();

    expect($quotation->status)->toBeInstanceOf(QuotationStatus::class)
        ->and($quotation->net_total)->toBe(130.0)
        ->and($quotation->grand_total)->toBe(130.0);
});

it('submits a quotation', function () {
    $quotation = Quotation::factory()->create();
    $quotation->items()->create(['item_code' => 'A', 'qty' => 2, 'rate' => 50]);
    $quotation->refresh();

    $quotation->submit();

    expect($quotation->docstatus)->toBe(DocStatus::Submitted)
        ->and($quotation->isSubmitted())->toBeTrue()
        ->and($quotation->grand_total)->toBe(100.0);
});

it('converts a quotation into a sales order and marks it ordered', function () {
    $quotation = Quotation::factory()->create(['customer_name' => 'Acme Inc']);
    $quotation->items()->create(['item_code' => 'WIDGET', 'item_name' => 'Widget', 'qty' => 3, 'rate' => 20]);
    $quotation->submit();

    $salesOrder = app(QuotationService::class)->createSalesOrder($quotation);

    expect($salesOrder)->toBeInstanceOf(SalesOrder::class)
        ->and($salesOrder->customer_name)->toBe('Acme Inc')
        ->and($salesOrder->items)->toHaveCount(1)
        ->and($salesOrder->items->first()->item_code)->toBe('WIDGET')
        ->and($salesOrder->items->first()->qty)->toBe(3.0)
        ->and($salesOrder->items->first()->rate)->toBe(20.0)
        ->and($salesOrder->grand_total)->toBe(60.0);

    expect($quotation->fresh()->status)->toBe(QuotationStatus::Ordered);
});
