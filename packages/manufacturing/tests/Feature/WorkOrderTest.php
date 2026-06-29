<?php

use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Manufacturing\Enums\WorkOrderStatus;
use JeffersonGoncalves\Erp\Manufacturing\Models\Bom;
use JeffersonGoncalves\Erp\Manufacturing\Models\WorkOrder;

it('populates the required items from the BOM scaled by quantity on submit', function () {
    $bom = Bom::factory()->create(['quantity' => 1, 'item_code' => 'FG-1']);
    $bom->items()->create(['item_code' => 'RAW-A', 'item_name' => 'Raw A', 'qty' => 2, 'rate' => 5]);
    $bom->items()->create(['item_code' => 'RAW-B', 'item_name' => 'Raw B', 'qty' => 3, 'rate' => 4]);

    $workOrder = WorkOrder::factory()->create([
        'production_item' => 'FG-1',
        'bom_id' => $bom->id,
        'qty' => 10,
    ]);

    $workOrder->submit();
    $workOrder->refresh();

    expect($workOrder->docstatus)->toBe(DocStatus::Submitted)
        ->and($workOrder->status)->toBe(WorkOrderStatus::NotStarted)
        ->and($workOrder->requiredItems)->toHaveCount(2);

    $rawA = $workOrder->requiredItems->firstWhere('item_code', 'RAW-A');
    $rawB = $workOrder->requiredItems->firstWhere('item_code', 'RAW-B');

    expect($rawA->required_qty)->toBe(20.0)
        ->and($rawB->required_qty)->toBe(30.0);
});

it('does not overwrite manually entered required items on submit', function () {
    $bom = Bom::factory()->create(['quantity' => 1]);
    $bom->items()->create(['item_code' => 'RAW-A', 'qty' => 2, 'rate' => 5]);

    $workOrder = WorkOrder::factory()->create(['bom_id' => $bom->id, 'qty' => 10]);
    $workOrder->requiredItems()->create(['item_code' => 'CUSTOM', 'required_qty' => 7]);

    $workOrder->submit();
    $workOrder->refresh();

    expect($workOrder->requiredItems)->toHaveCount(1)
        ->and($workOrder->requiredItems->first()->item_code)->toBe('CUSTOM');
});
