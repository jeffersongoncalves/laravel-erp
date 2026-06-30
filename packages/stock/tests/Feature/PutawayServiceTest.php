<?php

use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Stock\Models\Bin;
use JeffersonGoncalves\Erp\Stock\Models\PutawayRule;
use JeffersonGoncalves\Erp\Stock\Models\Warehouse;
use JeffersonGoncalves\Erp\Stock\Services\PutawayService;

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->item = stockItem();
    $this->primary = Warehouse::factory()->create(['company_id' => $this->company->id]);
    $this->secondary = Warehouse::factory()->create(['company_id' => $this->company->id]);
});

it('allocates across rules ordered by priority, capping at capacity', function () {
    PutawayRule::factory()->create([
        'item_id' => $this->item->id,
        'warehouse_id' => $this->primary->id,
        'company_id' => $this->company->id,
        'capacity' => 10,
        'priority' => 1,
    ]);

    PutawayRule::factory()->create([
        'item_id' => $this->item->id,
        'warehouse_id' => $this->secondary->id,
        'company_id' => $this->company->id,
        'capacity' => 50,
        'priority' => 2,
    ]);

    $result = app(PutawayService::class)->suggest($this->item->id, 30);

    expect($result)->toBe([
        ['warehouse_id' => $this->primary->id, 'qty' => 10.0],
        ['warehouse_id' => $this->secondary->id, 'qty' => 20.0],
        ['unassigned' => 0.0],
    ]);
});

it('reduces available capacity by the current bin balance', function () {
    Bin::query()->create([
        'item_id' => $this->item->id,
        'warehouse_id' => $this->primary->id,
        'actual_qty' => 7,
    ]);

    PutawayRule::factory()->create([
        'item_id' => $this->item->id,
        'warehouse_id' => $this->primary->id,
        'company_id' => $this->company->id,
        'capacity' => 10,
        'priority' => 1,
    ]);

    $result = app(PutawayService::class)->suggest($this->item->id, 5);

    expect($result)->toBe([
        ['warehouse_id' => $this->primary->id, 'qty' => 3.0],
        ['unassigned' => 2.0],
    ]);
});

it('returns overflow as unassigned when capacity is exhausted', function () {
    PutawayRule::factory()->create([
        'item_id' => $this->item->id,
        'warehouse_id' => $this->primary->id,
        'company_id' => $this->company->id,
        'capacity' => 4,
        'priority' => 1,
    ]);

    $result = app(PutawayService::class)->suggest($this->item->id, 10);

    expect($result)->toBe([
        ['warehouse_id' => $this->primary->id, 'qty' => 4.0],
        ['unassigned' => 6.0],
    ]);
});

it('skips disabled rules', function () {
    PutawayRule::factory()->disabled()->create([
        'item_id' => $this->item->id,
        'warehouse_id' => $this->primary->id,
        'company_id' => $this->company->id,
        'capacity' => 100,
        'priority' => 1,
    ]);

    $result = app(PutawayService::class)->suggest($this->item->id, 8);

    expect($result)->toBe([
        ['unassigned' => 8.0],
    ]);
});
