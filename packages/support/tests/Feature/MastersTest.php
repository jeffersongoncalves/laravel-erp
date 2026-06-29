<?php

use JeffersonGoncalves\Erp\Support\Enums\IssuePriority;
use JeffersonGoncalves\Erp\Support\Models\Issue;
use JeffersonGoncalves\Erp\Support\Models\IssueType;
use JeffersonGoncalves\Erp\Support\Models\ServiceLevelAgreement;

it('creates an issue type and relates its issues', function () {
    $type = IssueType::factory()->create();
    $issue = Issue::factory()->create(['issue_type_id' => $type->id]);

    expect($type->name)->not->toBeEmpty()
        ->and($type->issues->pluck('id'))->toContain($issue->id)
        ->and($issue->issueType->id)->toBe($type->id);
});

it('creates a service level agreement with default attributes', function () {
    $sla = ServiceLevelAgreement::factory()->create();

    expect($sla->default_sla)->toBeFalse()
        ->and($sla->enabled)->toBeTrue()
        ->and($sla->default_priority)->toBe(IssuePriority::Medium)
        ->and($sla->company->id)->toBe($sla->company_id);
});

it('flags a default service level agreement', function () {
    $sla = ServiceLevelAgreement::factory()->default()->create();

    expect($sla->default_sla)->toBeTrue();
});

it('creates a service level agreement with its priorities', function () {
    $sla = ServiceLevelAgreement::factory()->create();

    $sla->priorities()->create([
        'priority' => IssuePriority::High->value,
        'response_time' => 60,
        'resolution_time' => 480,
    ]);
    $sla->priorities()->create([
        'priority' => IssuePriority::Low->value,
        'response_time' => 240,
        'resolution_time' => 1440,
    ]);

    $sla->refresh();

    expect($sla->priorities)->toHaveCount(2)
        ->and($sla->priorities->first()->priority)->toBe(IssuePriority::High)
        ->and($sla->priorities->first()->response_time)->toBe(60)
        ->and($sla->priorities->first()->serviceLevelAgreement->id)->toBe($sla->id);
});
