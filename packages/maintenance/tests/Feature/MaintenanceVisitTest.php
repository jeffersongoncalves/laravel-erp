<?php

use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Maintenance\Enums\CompletionStatus;
use JeffersonGoncalves\Erp\Maintenance\Enums\MaintenanceType;
use JeffersonGoncalves\Erp\Maintenance\Enums\MaintenanceVisitPurposeType;
use JeffersonGoncalves\Erp\Maintenance\Models\MaintenanceSchedule;
use JeffersonGoncalves\Erp\Maintenance\Models\MaintenanceVisit;

it('creates a maintenance visit with its purposes and defaults', function () {
    $visit = MaintenanceVisit::factory()->create();

    $visit->purposes()->create([
        'item_code' => 'PUMP-01',
        'description' => 'Inspect pump',
        'purpose_type' => MaintenanceVisitPurposeType::Maintenance->value,
    ]);

    $visit->refresh();

    expect($visit->party_type)->toBe('Customer')
        ->and($visit->maintenance_type)->toBeInstanceOf(MaintenanceType::class)
        ->and($visit->maintenance_type)->toBe(MaintenanceType::Scheduled)
        ->and($visit->completion_status)->toBe(CompletionStatus::Pending)
        ->and($visit->status)->toBe('Draft')
        ->and($visit->docstatus)->toBe(DocStatus::Draft)
        ->and($visit->company->id)->toBe($visit->company_id)
        ->and($visit->purposes)->toHaveCount(1)
        ->and($visit->purposes->first()->purpose_type)->toBe(MaintenanceVisitPurposeType::Maintenance)
        ->and($visit->purposes->first()->maintenanceVisit->id)->toBe($visit->id);
});

it('recomputes the completion status from its purposes work done', function () {
    $visit = MaintenanceVisit::factory()->create();

    $first = $visit->purposes()->create(['item_code' => 'A']);
    $second = $visit->purposes()->create(['item_code' => 'B']);

    expect($visit->fresh()->completion_status)->toBe(CompletionStatus::Pending);

    $first->update(['work_done' => 'Replaced filter']);

    expect($visit->fresh()->completion_status)->toBe(CompletionStatus::PartiallyCompleted);

    $second->update(['work_done' => 'Cleaned condenser coil']);

    expect($visit->fresh()->completion_status)->toBe(CompletionStatus::FullyCompleted);
});

it('submits a maintenance visit', function () {
    $visit = MaintenanceVisit::factory()->create();

    $visit->submit();

    expect($visit->docstatus)->toBe(DocStatus::Submitted)
        ->and($visit->isSubmitted())->toBeTrue();
});

it('relates a maintenance visit to its schedule', function () {
    $schedule = MaintenanceSchedule::factory()->create();
    $visit = MaintenanceVisit::factory()->create(['maintenance_schedule_id' => $schedule->id]);

    expect($visit->maintenanceSchedule->id)->toBe($schedule->id)
        ->and($schedule->visits)->toHaveCount(1)
        ->and($schedule->visits->first()->id)->toBe($visit->id);
});
