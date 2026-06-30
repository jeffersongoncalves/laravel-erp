<?php

use JeffersonGoncalves\Erp\Buying\Models\BlanketOrder;
use JeffersonGoncalves\Erp\Buying\Models\BlanketOrderItem;
use JeffersonGoncalves\Erp\Buying\Services\BlanketOrderService;
use JeffersonGoncalves\Erp\Core\Models\Company;

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->service = app(BlanketOrderService::class);
});

function blanketOrderItem(float $qty, float $ordered = 0): BlanketOrderItem
{
    $order = BlanketOrder::factory()->create(['company_id' => test()->company->id]);

    return $order->items()->create([
        'item_code' => 'RAW',
        'qty' => $qty,
        'rate' => 5,
        'ordered_qty' => $ordered,
    ]);
}

it('reports the available quantity as qty minus ordered', function () {
    $item = blanketOrderItem(100, 30);

    expect($this->service->availableQty($item))->toBe(70.0);
});

it('records ordered quantity and decreases the availability', function () {
    $item = blanketOrderItem(100);

    $this->service->recordOrdered($item, 40);

    expect($item->refresh()->ordered_qty)->toBe(40.0)
        ->and($this->service->availableQty($item))->toBe(60.0);
});

it('accumulates repeated draw-downs', function () {
    $item = blanketOrderItem(100);

    $this->service->recordOrdered($item, 40);
    $this->service->recordOrdered($item, 25);

    expect($item->refresh()->ordered_qty)->toBe(65.0);
});

it('throws when the ordered quantity exceeds the agreement', function () {
    $item = blanketOrderItem(100, 80);

    expect(fn () => $this->service->recordOrdered($item, 25))
        ->toThrow(DomainException::class);

    expect($item->refresh()->ordered_qty)->toBe(80.0);
});
