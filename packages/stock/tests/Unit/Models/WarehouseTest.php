<?php

use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Stock\Models\Warehouse;

it('uses the configured table prefix', function () {
    expect((new Warehouse)->getTable())->toBe('erp_warehouses');
});

it('builds a parent and children tree', function () {
    $parent = Warehouse::factory()->group()->create();
    $child = Warehouse::factory()->create(['parent_warehouse_id' => $parent->id]);

    expect($child->parent->id)->toBe($parent->id)
        ->and($parent->children)->toHaveCount(1)
        ->and($parent->is_group)->toBeTrue();
});

it('relates to its inventory account', function () {
    $account = Account::factory()->create();
    $warehouse = Warehouse::factory()->create(['account_id' => $account->id]);

    expect($warehouse->account->id)->toBe($account->id);
});

it('scopes leaf warehouses', function () {
    Warehouse::factory()->group()->create();
    Warehouse::factory()->create();

    expect(Warehouse::query()->leaf()->count())->toBe(1);
});
