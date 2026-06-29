<?php

use JeffersonGoncalves\Erp\Support\Enums\IssuePriority;
use JeffersonGoncalves\Erp\Support\Enums\IssueStatus;
use JeffersonGoncalves\Erp\Support\Models\Issue;
use JeffersonGoncalves\Erp\Support\Models\IssueType;
use JeffersonGoncalves\Erp\Support\Models\ServiceLevelAgreement;

it('creates an issue with the default status and priority', function () {
    $issue = Issue::factory()->create();

    expect($issue->status)->toBeInstanceOf(IssueStatus::class)
        ->and($issue->status)->toBe(IssueStatus::Open)
        ->and($issue->priority)->toBe(IssuePriority::Medium)
        ->and($issue->party_type)->toBe('Customer');
});

it('casts the issue status to the enum', function () {
    $issue = Issue::factory()->create(['status' => 'Replied']);

    expect($issue->status)->toBe(IssueStatus::Replied);
});

it('transitions an issue through its statuses', function () {
    $issue = Issue::factory()->create();

    $issue->update(['status' => IssueStatus::OnHold->value]);
    expect($issue->fresh()->status)->toBe(IssueStatus::OnHold);

    $issue->update(['status' => IssueStatus::Resolved->value]);
    expect($issue->fresh()->status)->toBe(IssueStatus::Resolved);
});

it('scopes out resolved and closed issues', function () {
    Issue::factory()->create(['status' => 'Open']);
    Issue::factory()->create(['status' => 'Resolved']);
    Issue::factory()->create(['status' => 'Closed']);

    expect(Issue::open()->count())->toBe(1);
});

it('changes the priority of an issue', function () {
    $issue = Issue::factory()->create(['priority' => 'Urgent']);

    expect($issue->priority)->toBe(IssuePriority::Urgent);
});

it('relates an issue to its type and service level agreement', function () {
    $type = IssueType::factory()->create();
    $sla = ServiceLevelAgreement::factory()->create();

    $issue = Issue::factory()->create([
        'issue_type_id' => $type->id,
        'service_level_agreement_id' => $sla->id,
    ]);

    expect($issue->issueType->id)->toBe($type->id)
        ->and($issue->serviceLevelAgreement->id)->toBe($sla->id)
        ->and($sla->issues->pluck('id'))->toContain($issue->id);
});
