<?php

use JeffersonGoncalves\Erp\Core\Enums\AddressType;

it('has the expected cases', function () {
    expect(AddressType::Billing->value)->toBe('Billing')
        ->and(AddressType::Shipping->value)->toBe('Shipping')
        ->and(AddressType::Office->value)->toBe('Office')
        ->and(AddressType::Other->value)->toBe('Other');
});

it('returns a label for each case', function () {
    expect(AddressType::Billing->label())->toBeString()
        ->and(AddressType::Shipping->label())->toBeString();
});
