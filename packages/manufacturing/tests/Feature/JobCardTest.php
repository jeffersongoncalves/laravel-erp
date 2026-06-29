<?php

use JeffersonGoncalves\Erp\Manufacturing\Enums\JobCardStatus;
use JeffersonGoncalves\Erp\Manufacturing\Models\JobCard;
use JeffersonGoncalves\Erp\Manufacturing\Models\Operation;
use JeffersonGoncalves\Erp\Manufacturing\Models\WorkOrder;
use JeffersonGoncalves\Erp\Manufacturing\Models\Workstation;

it('creates a job card against a work order with a time log', function () {
    $workOrder = WorkOrder::factory()->create();
    $workstation = Workstation::factory()->create();
    $operation = Operation::factory()->create(['workstation_id' => $workstation->id]);

    $jobCard = JobCard::factory()->create([
        'work_order_id' => $workOrder->id,
        'operation_id' => $operation->id,
        'workstation_id' => $workstation->id,
        'for_quantity' => 25,
    ]);

    $jobCard->timeLogs()->create([
        'from_time' => now(),
        'to_time' => now()->addMinutes(45),
        'time_in_mins' => 45,
        'completed_qty' => 25,
    ]);

    $jobCard->refresh();

    expect($jobCard->status)->toBe(JobCardStatus::Open)
        ->and($jobCard->workOrder->id)->toBe($workOrder->id)
        ->and($jobCard->operation->id)->toBe($operation->id)
        ->and($jobCard->timeLogs)->toHaveCount(1)
        ->and($jobCard->timeLogs->first()->time_in_mins)->toBe(45.0);
});
