<?php

use JeffersonGoncalves\Erp\Crm\Models\Campaign;
use JeffersonGoncalves\Erp\Crm\Models\Contract;
use JeffersonGoncalves\Erp\Crm\Models\Lead;
use JeffersonGoncalves\Erp\Crm\Models\LeadSource;

it('creates a lead source and relates its leads', function () {
    $source = LeadSource::factory()->create();
    $lead = Lead::factory()->create(['lead_source_id' => $source->id]);

    expect($source->name)->not->toBeEmpty()
        ->and($source->leads->pluck('id'))->toContain($lead->id)
        ->and($lead->leadSource->id)->toBe($source->id);
});

it('creates a campaign and relates its leads', function () {
    $campaign = Campaign::factory()->create();
    $lead = Lead::factory()->create(['campaign_id' => $campaign->id]);

    expect($campaign->campaign_name)->not->toBeEmpty()
        ->and($campaign->leads->pluck('id'))->toContain($lead->id)
        ->and($lead->campaign->id)->toBe($campaign->id);
});

it('creates a contract with default attributes', function () {
    $contract = Contract::factory()->create();

    expect($contract->party_type)->toBe('Customer')
        ->and($contract->status)->toBe('Unsigned')
        ->and($contract->is_signed)->toBeFalse()
        ->and($contract->company->id)->toBe($contract->company_id);
});

it('flags a signed contract', function () {
    $contract = Contract::factory()->signed()->create();

    expect($contract->is_signed)->toBeTrue()
        ->and($contract->status)->toBe('Active');
});
