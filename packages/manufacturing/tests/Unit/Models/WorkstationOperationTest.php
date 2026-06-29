<?php

use JeffersonGoncalves\Erp\Manufacturing\Models\Operation;
use JeffersonGoncalves\Erp\Manufacturing\Models\Workstation;

it('creates a workstation master with defaults', function () {
    $workstation = Workstation::factory()->create([
        'name' => 'Assembly Line 1',
        'hour_rate' => 45,
        'production_capacity' => 3,
    ]);

    expect($workstation->name)->toBe('Assembly Line 1')
        ->and($workstation->hour_rate)->toBe(45.0)
        ->and($workstation->production_capacity)->toBe(3);
});

it('links an operation to its workstation', function () {
    $workstation = Workstation::factory()->create();

    $operation = Operation::factory()->create([
        'name' => 'Cutting',
        'workstation_id' => $workstation->id,
    ]);

    expect($operation->workstation->id)->toBe($workstation->id)
        ->and($workstation->operations)->toHaveCount(1);
});
