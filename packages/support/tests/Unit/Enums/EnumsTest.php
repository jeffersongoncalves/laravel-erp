<?php

use JeffersonGoncalves\Erp\Support\Enums\AgreementStatus;
use JeffersonGoncalves\Erp\Support\Enums\IssuePriority;
use JeffersonGoncalves\Erp\Support\Enums\IssueStatus;
use JeffersonGoncalves\Erp\Support\Enums\WarrantyClaimStatus;

it('exposes the issue statuses', function () {
    expect(IssueStatus::cases())->toHaveCount(5)
        ->and(IssueStatus::Open->value)->toBe('Open')
        ->and(IssueStatus::OnHold->value)->toBe('On Hold')
        ->and(IssueStatus::Closed->value)->toBe('Closed');
});

it('exposes the issue priorities', function () {
    expect(IssuePriority::cases())->toHaveCount(4)
        ->and(IssuePriority::Low->value)->toBe('Low')
        ->and(IssuePriority::Urgent->value)->toBe('Urgent');
});

it('exposes the warranty claim statuses', function () {
    expect(WarrantyClaimStatus::cases())->toHaveCount(5)
        ->and(WarrantyClaimStatus::Open->value)->toBe('Open')
        ->and(WarrantyClaimStatus::InProgress->value)->toBe('In Progress')
        ->and(WarrantyClaimStatus::Cancelled->value)->toBe('Cancelled');
});

it('exposes the agreement statuses', function () {
    expect(AgreementStatus::cases())->toHaveCount(5)
        ->and(AgreementStatus::FirstResponseDue->value)->toBe('First Response Due')
        ->and(AgreementStatus::ResolutionDue->value)->toBe('Resolution Due')
        ->and(AgreementStatus::Paused->value)->toBe('Paused');
});
