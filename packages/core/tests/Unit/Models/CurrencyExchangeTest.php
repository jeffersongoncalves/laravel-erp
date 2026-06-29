<?php

use JeffersonGoncalves\Erp\Core\Models\CurrencyExchange;

it('uses the configured table prefix', function () {
    expect((new CurrencyExchange)->getTable())->toBe('erp_currency_exchanges');
});

it('can be created and casts its values', function () {
    $exchange = CurrencyExchange::create([
        'from_currency' => 'USD',
        'to_currency' => 'EUR',
        'exchange_rate' => '0.920000000',
        'date' => '2026-06-26',
    ]);

    expect($exchange->from_currency)->toBe('USD')
        ->and($exchange->to_currency)->toBe('EUR')
        ->and($exchange->date->format('Y-m-d'))->toBe('2026-06-26');
});
