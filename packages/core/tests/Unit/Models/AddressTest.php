<?php

use JeffersonGoncalves\Erp\Core\Enums\AddressType;
use JeffersonGoncalves\Erp\Core\Models\Address;
use JeffersonGoncalves\Erp\Core\Models\Company;

it('uses the configured table prefix', function () {
    expect((new Address)->getTable())->toBe('erp_addresses');
});

it('can be created via factory and casts the address type', function () {
    $address = Address::factory()->create();

    expect($address->address_type)->toBe(AddressType::Billing)
        ->and($address->is_primary)->toBeFalse();
});

it('resolves the polymorphic addressable relationship', function () {
    $company = Company::factory()->create();
    $address = Address::factory()->create([
        'addressable_type' => $company->getMorphClass(),
        'addressable_id' => $company->id,
    ]);

    expect($address->addressable->id)->toBe($company->id)
        ->and($address->addressable)->toBeInstanceOf(Company::class);
});

it('scopes primary addresses', function () {
    Address::factory()->primary()->create();
    Address::factory()->create();

    expect(Address::primary()->count())->toBe(1);
});
