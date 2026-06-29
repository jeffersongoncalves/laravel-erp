<?php

use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Core\Models\Contact;

it('uses the configured table prefix', function () {
    expect((new Contact)->getTable())->toBe('erp_contacts');
});

it('can be created via factory', function () {
    $contact = Contact::factory()->create(['first_name' => 'Jane']);

    expect($contact->first_name)->toBe('Jane')
        ->and($contact->is_primary)->toBeFalse();
});

it('resolves the polymorphic contactable relationship', function () {
    $company = Company::factory()->create();
    $contact = Contact::factory()->create([
        'contactable_type' => $company->getMorphClass(),
        'contactable_id' => $company->id,
    ]);

    expect($contact->contactable->id)->toBe($company->id)
        ->and($contact->contactable)->toBeInstanceOf(Company::class);
});
