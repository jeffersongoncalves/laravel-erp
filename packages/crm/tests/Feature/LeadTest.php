<?php

use JeffersonGoncalves\Erp\Crm\Enums\LeadStatus;
use JeffersonGoncalves\Erp\Crm\Models\Lead;

it('creates a lead with the default status', function () {
    $lead = Lead::factory()->create();

    expect($lead->status)->toBeInstanceOf(LeadStatus::class)
        ->and($lead->status)->toBe(LeadStatus::Lead);
});

it('casts the lead status to the enum', function () {
    $lead = Lead::factory()->create(['status' => 'Replied']);

    expect($lead->status)->toBe(LeadStatus::Replied);
});

it('scopes out converted and do-not-contact leads', function () {
    Lead::factory()->create(['status' => 'Lead']);
    Lead::factory()->create(['status' => 'Converted']);
    Lead::factory()->create(['status' => 'Do Not Contact']);

    expect(Lead::open()->count())->toBe(1);
});
