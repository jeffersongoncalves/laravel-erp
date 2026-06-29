<?php

use JeffersonGoncalves\Erp\Core\Models\Currency;

it('uses the configured table prefix', function () {
    expect((new Currency)->getTable())->toBe('erp_currencies');
});

it('can be created via factory', function () {
    $currency = Currency::factory()->create(['code' => 'USD', 'name' => 'US Dollar']);

    expect($currency->code)->toBe('USD')
        ->and($currency->enabled)->toBeTrue();
});

it('scopes enabled currencies', function () {
    Currency::factory()->create();
    Currency::factory()->disabled()->create();

    expect(Currency::enabled()->count())->toBe(1);
});
