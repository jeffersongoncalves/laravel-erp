<?php

use JeffersonGoncalves\Erp\Crm\Enums\LeadStatus;
use JeffersonGoncalves\Erp\Crm\Enums\OpportunityStatus;
use JeffersonGoncalves\Erp\Crm\Enums\OpportunityType;

it('exposes the lead statuses', function () {
    expect(LeadStatus::cases())->toHaveCount(7)
        ->and(LeadStatus::Lead->value)->toBe('Lead')
        ->and(LeadStatus::Converted->value)->toBe('Converted')
        ->and(LeadStatus::DoNotContact->value)->toBe('Do Not Contact');
});

it('exposes the opportunity statuses', function () {
    expect(OpportunityStatus::cases())->toHaveCount(6)
        ->and(OpportunityStatus::Open->value)->toBe('Open')
        ->and(OpportunityStatus::Quotation->value)->toBe('Quotation');
});

it('exposes the opportunity types', function () {
    expect(OpportunityType::cases())->toHaveCount(3)
        ->and(OpportunityType::Sales->value)->toBe('Sales')
        ->and(OpportunityType::Maintenance->value)->toBe('Maintenance');
});
