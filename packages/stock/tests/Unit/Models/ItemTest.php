<?php

use JeffersonGoncalves\Erp\Core\Models\Uom;
use JeffersonGoncalves\Erp\Stock\Enums\ValuationMethod;
use JeffersonGoncalves\Erp\Stock\Models\Item;
use JeffersonGoncalves\Erp\Stock\Models\Warehouse;

it('uses the configured table prefix', function () {
    expect((new Item)->getTable())->toBe('erp_items');
});

it('casts flags, rate and valuation method', function () {
    $item = Item::factory()->valuation(ValuationMethod::FIFO)->create([
        'standard_rate' => 12.5,
        'has_batch_no' => true,
    ]);

    expect($item->refresh()->valuation_method)->toBe(ValuationMethod::FIFO)
        ->and($item->is_stock_item)->toBeTrue()
        ->and($item->has_batch_no)->toBeTrue()
        ->and($item->standard_rate)->toBe(12.5);
});

it('relates to its stock uom and default warehouse', function () {
    $uom = Uom::factory()->create();
    $warehouse = Warehouse::factory()->create();

    $item = Item::factory()->create([
        'stock_uom_id' => $uom->id,
        'default_warehouse_id' => $warehouse->id,
    ]);

    expect($item->stockUom->id)->toBe($uom->id)
        ->and($item->defaultWarehouse->id)->toBe($warehouse->id);
});

it('scopes stock items', function () {
    Item::factory()->create();
    Item::factory()->nonStock()->create();

    expect(Item::query()->stockItems()->count())->toBe(1);
});
