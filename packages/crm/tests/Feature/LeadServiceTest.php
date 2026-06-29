<?php

use JeffersonGoncalves\Erp\Crm\Enums\LeadStatus;
use JeffersonGoncalves\Erp\Crm\Models\Lead;
use JeffersonGoncalves\Erp\Crm\Models\Opportunity;
use JeffersonGoncalves\Erp\Crm\Services\LeadService;
use JeffersonGoncalves\Erp\Selling\Models\Customer;

it('converts a lead into a selling customer and flags it converted', function () {
    $lead = Lead::factory()->create([
        'lead_name' => 'Acme Contact',
        'company_name' => null,
        'status' => 'Open',
    ]);

    $customer = app(LeadService::class)->createCustomer($lead);

    expect($customer)->toBeInstanceOf(Customer::class)
        ->and($customer->exists)->toBeTrue()
        ->and($customer->customer_name)->toBe('Acme Contact');

    expect($lead->fresh()->status)->toBe(LeadStatus::Converted)
        ->and($lead->fresh()->customer_id)->toBe($customer->id);
});

it('uses the company name for the customer when present', function () {
    $lead = Lead::factory()->create([
        'lead_name' => 'Jane Doe',
        'company_name' => 'Globex Corporation',
    ]);

    $customer = app(LeadService::class)->createCustomer($lead);

    expect($customer->customer_name)->toBe('Globex Corporation');
});

it('converts a lead into an opportunity and moves it to the opportunity stage', function () {
    $lead = Lead::factory()->create([
        'lead_name' => 'Wayne Enterprises',
        'company_name' => 'Wayne Enterprises',
    ]);

    $opportunity = app(LeadService::class)->createOpportunity($lead);

    expect($opportunity)->toBeInstanceOf(Opportunity::class)
        ->and($opportunity->exists)->toBeTrue()
        ->and($opportunity->opportunity_from)->toBe('Lead')
        ->and($opportunity->lead_id)->toBe($lead->id)
        ->and($opportunity->party_name)->toBe('Wayne Enterprises');

    expect($lead->fresh()->status)->toBe(LeadStatus::Opportunity);
});
