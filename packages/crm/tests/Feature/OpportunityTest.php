<?php

use JeffersonGoncalves\Erp\Crm\Enums\OpportunityStatus;
use JeffersonGoncalves\Erp\Crm\Enums\OpportunityType;
use JeffersonGoncalves\Erp\Crm\Models\Opportunity;

it('creates an opportunity with default attributes', function () {
    $opportunity = Opportunity::factory()->create();

    expect($opportunity->status)->toBeInstanceOf(OpportunityStatus::class)
        ->and($opportunity->status)->toBe(OpportunityStatus::Open)
        ->and($opportunity->opportunity_type)->toBe(OpportunityType::Sales)
        ->and($opportunity->opportunity_from)->toBe('Lead');
});

it('recomputes the amount from its items', function () {
    $opportunity = Opportunity::factory()->create();

    $opportunity->items()->create(['item_code' => 'A', 'qty' => 2, 'rate' => 50]);
    $opportunity->items()->create(['item_code' => 'B', 'qty' => 1, 'rate' => 30]);

    $opportunity->refresh();

    expect($opportunity->items)->toHaveCount(2)
        ->and($opportunity->items->first()->amount)->toBe(100.0)
        ->and($opportunity->opportunity_amount)->toBe(130.0);
});
