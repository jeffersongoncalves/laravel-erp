<?php

use JeffersonGoncalves\Erp\Manufacturing\Enums\BomStatus;
use JeffersonGoncalves\Erp\Manufacturing\Enums\JobCardStatus;
use JeffersonGoncalves\Erp\Manufacturing\Enums\WorkOrderStatus;

it('exposes the BOM statuses', function () {
    expect(BomStatus::Draft->value)->toBe('Draft')
        ->and(BomStatus::Active->value)->toBe('Active')
        ->and(BomStatus::Inactive->value)->toBe('Inactive');
});

it('exposes the work order statuses', function () {
    expect(WorkOrderStatus::Draft->value)->toBe('Draft')
        ->and(WorkOrderStatus::NotStarted->value)->toBe('Not Started')
        ->and(WorkOrderStatus::InProcess->value)->toBe('In Process')
        ->and(WorkOrderStatus::Completed->value)->toBe('Completed')
        ->and(WorkOrderStatus::Stopped->value)->toBe('Stopped')
        ->and(WorkOrderStatus::Cancelled->value)->toBe('Cancelled');
});

it('exposes the job card statuses', function () {
    expect(JobCardStatus::Open->value)->toBe('Open')
        ->and(JobCardStatus::WorkInProgress->value)->toBe('Work In Progress')
        ->and(JobCardStatus::Completed->value)->toBe('Completed')
        ->and(JobCardStatus::OnHold->value)->toBe('On Hold')
        ->and(JobCardStatus::Cancelled->value)->toBe('Cancelled');
});
