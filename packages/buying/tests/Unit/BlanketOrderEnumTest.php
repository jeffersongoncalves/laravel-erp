<?php

use JeffersonGoncalves\Erp\Buying\Enums\BlanketOrderType;

it('exposes the blanket order types', function () {
    expect(BlanketOrderType::Purchasing->value)->toBe('Purchasing')
        ->and(BlanketOrderType::Selling->value)->toBe('Selling')
        ->and(BlanketOrderType::cases())->toHaveCount(2);
});
