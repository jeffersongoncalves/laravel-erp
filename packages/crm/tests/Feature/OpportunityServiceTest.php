<?php

use JeffersonGoncalves\Erp\Crm\Enums\OpportunityStatus;
use JeffersonGoncalves\Erp\Crm\Models\Opportunity;
use JeffersonGoncalves\Erp\Crm\Services\OpportunityService;
use JeffersonGoncalves\Erp\Selling\Models\Quotation;

it('converts an opportunity into a draft quotation with matching items', function () {
    $opportunity = Opportunity::factory()->create([
        'party_type' => 'Customer',
        'party_name' => 'Stark Industries',
    ]);
    $opportunity->items()->create(['item_code' => 'WIDGET', 'item_name' => 'Widget', 'qty' => 3, 'rate' => 20]);
    $opportunity->items()->create(['item_code' => 'GADGET', 'item_name' => 'Gadget', 'qty' => 2, 'rate' => 15]);

    $quotation = app(OpportunityService::class)->createQuotation($opportunity);

    expect($quotation)->toBeInstanceOf(Quotation::class)
        ->and($quotation->exists)->toBeTrue()
        ->and($quotation->customer_name)->toBe('Stark Industries')
        ->and($quotation->items)->toHaveCount(2)
        ->and($quotation->items->first()->item_code)->toBe('WIDGET')
        ->and($quotation->items->first()->qty)->toBe(3.0)
        ->and($quotation->items->first()->rate)->toBe(20.0)
        ->and($quotation->grand_total)->toBe(90.0);

    expect($opportunity->fresh()->status)->toBe(OpportunityStatus::Quotation);
});
