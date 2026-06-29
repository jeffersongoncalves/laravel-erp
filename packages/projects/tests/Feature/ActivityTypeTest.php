<?php

use JeffersonGoncalves\Erp\Projects\Models\ActivityType;

it('creates an activity type with default attributes', function () {
    $activityType = ActivityType::factory()->create([
        'name' => 'Development',
        'default_costing_rate' => '35.5',
        'default_billing_rate' => '90',
    ]);

    expect($activityType->name)->toBe('Development')
        ->and($activityType->default_costing_rate)->toBeFloat()->toBe(35.5)
        ->and($activityType->default_billing_rate)->toBeFloat()->toBe(90.0)
        ->and($activityType->disabled)->toBeFalse();
});

it('scopes active activity types', function () {
    ActivityType::factory()->create();
    ActivityType::factory()->disabled()->create();

    expect(ActivityType::query()->active()->count())->toBe(1);
});
