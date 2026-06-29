<?php

use JeffersonGoncalves\Erp\Projects\Enums\ProjectStatus;
use JeffersonGoncalves\Erp\Projects\Enums\TaskPriority;
use JeffersonGoncalves\Erp\Projects\Enums\TaskStatus;

it('exposes the project statuses', function () {
    expect(ProjectStatus::cases())->toHaveCount(3)
        ->and(ProjectStatus::Open->value)->toBe('Open')
        ->and(ProjectStatus::Completed->value)->toBe('Completed');
});

it('exposes the task statuses', function () {
    expect(TaskStatus::cases())->toHaveCount(6)
        ->and(TaskStatus::PendingReview->value)->toBe('Pending Review')
        ->and(TaskStatus::Working->value)->toBe('Working');
});

it('exposes the task priorities', function () {
    expect(TaskPriority::cases())->toHaveCount(4)
        ->and(TaskPriority::Urgent->value)->toBe('Urgent')
        ->and(TaskPriority::Medium->value)->toBe('Medium');
});
