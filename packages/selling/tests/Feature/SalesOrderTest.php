<?php

use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Selling\Enums\SalesOrderStatus;
use JeffersonGoncalves\Erp\Selling\Models\SalesOrder;

it('recomputes totals from its items while draft', function () {
    $salesOrder = SalesOrder::factory()->create();

    $salesOrder->items()->create(['item_code' => 'A', 'qty' => 4, 'rate' => 25]);
    $salesOrder->items()->create(['item_code' => 'B', 'qty' => 2, 'rate' => 10]);

    $salesOrder->refresh();

    expect($salesOrder->status)->toBeInstanceOf(SalesOrderStatus::class)
        ->and($salesOrder->net_total)->toBe(120.0)
        ->and($salesOrder->grand_total)->toBe(120.0);
});

it('defaults the per-delivered and per-billed percentages to zero', function () {
    $salesOrder = SalesOrder::factory()->create();

    expect($salesOrder->per_delivered)->toBe(0.0)
        ->and($salesOrder->per_billed)->toBe(0.0)
        ->and($salesOrder->status)->toBe(SalesOrderStatus::Draft);
});

it('submits a sales order', function () {
    $salesOrder = SalesOrder::factory()->create();
    $salesOrder->items()->create(['item_code' => 'A', 'qty' => 4, 'rate' => 25]);
    $salesOrder->refresh();

    $salesOrder->submit();

    expect($salesOrder->docstatus)->toBe(DocStatus::Submitted)
        ->and($salesOrder->isSubmitted())->toBeTrue()
        ->and($salesOrder->grand_total)->toBe(100.0);
});
