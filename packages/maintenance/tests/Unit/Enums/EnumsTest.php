<?php

use JeffersonGoncalves\Erp\Maintenance\Enums\CompletionStatus;
use JeffersonGoncalves\Erp\Maintenance\Enums\MaintenanceType;
use JeffersonGoncalves\Erp\Maintenance\Enums\MaintenanceVisitPurposeType;

it('exposes the maintenance types', function () {
    expect(MaintenanceType::cases())->toHaveCount(3)
        ->and(MaintenanceType::Scheduled->value)->toBe('Scheduled')
        ->and(MaintenanceType::Unscheduled->value)->toBe('Unscheduled')
        ->and(MaintenanceType::Breakdown->value)->toBe('Breakdown');
});

it('exposes the completion statuses', function () {
    expect(CompletionStatus::cases())->toHaveCount(3)
        ->and(CompletionStatus::Pending->value)->toBe('Pending')
        ->and(CompletionStatus::PartiallyCompleted->value)->toBe('Partially Completed')
        ->and(CompletionStatus::FullyCompleted->value)->toBe('Fully Completed');
});

it('exposes the maintenance visit purpose types', function () {
    expect(MaintenanceVisitPurposeType::cases())->toHaveCount(4)
        ->and(MaintenanceVisitPurposeType::Maintenance->value)->toBe('Maintenance')
        ->and(MaintenanceVisitPurposeType::Service->value)->toBe('Service')
        ->and(MaintenanceVisitPurposeType::Installation->value)->toBe('Installation')
        ->and(MaintenanceVisitPurposeType::Repair->value)->toBe('Repair');
});
