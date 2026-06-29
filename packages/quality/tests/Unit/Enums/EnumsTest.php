<?php

use JeffersonGoncalves\Erp\Quality\Enums\InspectionResult;
use JeffersonGoncalves\Erp\Quality\Enums\InspectionType;
use JeffersonGoncalves\Erp\Quality\Enums\NonConformanceStatus;
use JeffersonGoncalves\Erp\Quality\Enums\QualityActionStatus;
use JeffersonGoncalves\Erp\Quality\Enums\ReadingStatus;

it('exposes the inspection types', function () {
    expect(InspectionType::cases())->toHaveCount(3)
        ->and(InspectionType::Incoming->value)->toBe('Incoming')
        ->and(InspectionType::Outgoing->value)->toBe('Outgoing')
        ->and(InspectionType::InProcess->value)->toBe('In Process');
});

it('exposes the inspection results', function () {
    expect(InspectionResult::cases())->toHaveCount(2)
        ->and(InspectionResult::Accepted->value)->toBe('Accepted')
        ->and(InspectionResult::Rejected->value)->toBe('Rejected');
});

it('exposes the reading statuses', function () {
    expect(ReadingStatus::cases())->toHaveCount(2)
        ->and(ReadingStatus::Accepted->value)->toBe('Accepted')
        ->and(ReadingStatus::Rejected->value)->toBe('Rejected');
});

it('exposes the non conformance statuses', function () {
    expect(NonConformanceStatus::cases())->toHaveCount(5)
        ->and(NonConformanceStatus::Open->value)->toBe('Open')
        ->and(NonConformanceStatus::InProgress->value)->toBe('In Progress')
        ->and(NonConformanceStatus::Cancelled->value)->toBe('Cancelled');
});

it('exposes the quality action statuses', function () {
    expect(QualityActionStatus::cases())->toHaveCount(4)
        ->and(QualityActionStatus::Open->value)->toBe('Open')
        ->and(QualityActionStatus::InProgress->value)->toBe('In Progress')
        ->and(QualityActionStatus::Completed->value)->toBe('Completed')
        ->and(QualityActionStatus::Cancelled->value)->toBe('Cancelled');
});
