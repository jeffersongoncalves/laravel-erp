<?php

use JeffersonGoncalves\Erp\Manufacturing\Models\Bom;
use JeffersonGoncalves\Erp\Manufacturing\Models\Workstation;

it('recomputes the BOM costs from its item and operation lines', function () {
    $bom = Bom::factory()->create(['quantity' => 1]);

    $bom->items()->create(['item_code' => 'RAW-A', 'qty' => 2, 'rate' => 5]);   // 10
    $bom->items()->create(['item_code' => 'RAW-B', 'qty' => 1, 'rate' => 20]);  // 20

    $workstation = Workstation::factory()->create();
    $bom->operations()->create([
        'workstation_id' => $workstation->id,
        'time_in_mins' => 60,
        'hour_rate' => 30,
    ]); // 60/60 * 30 = 30

    $bom->refresh();

    expect($bom->raw_material_cost)->toBe(30.0)
        ->and($bom->operating_cost)->toBe(30.0)
        ->and($bom->total_cost)->toBe(60.0);
});

it('derives the line amount and operating cost on the child rows', function () {
    $bom = Bom::factory()->create(['quantity' => 1]);

    $item = $bom->items()->create(['item_code' => 'RAW-A', 'qty' => 4, 'rate' => 2.5]);
    $operation = $bom->operations()->create(['time_in_mins' => 90, 'hour_rate' => 40]);

    expect($item->amount)->toBe(10.0)
        ->and($operation->operating_cost)->toBe(60.0);
});
