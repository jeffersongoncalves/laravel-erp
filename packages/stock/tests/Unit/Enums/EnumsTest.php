<?php

use JeffersonGoncalves\Erp\Stock\Enums\SerialNoStatus;
use JeffersonGoncalves\Erp\Stock\Enums\StockEntryType;
use JeffersonGoncalves\Erp\Stock\Enums\ValuationMethod;

it('exposes the valuation methods', function () {
    expect(ValuationMethod::FIFO->value)->toBe('FIFO')
        ->and(ValuationMethod::MovingAverage->value)->toBe('Moving Average');
});

it('exposes the stock entry types', function () {
    expect(StockEntryType::MaterialReceipt->value)->toBe('Material Receipt')
        ->and(StockEntryType::MaterialIssue->value)->toBe('Material Issue')
        ->and(StockEntryType::MaterialTransfer->value)->toBe('Material Transfer')
        ->and(StockEntryType::cases())->toHaveCount(6);
});

it('exposes the serial number statuses', function () {
    expect(SerialNoStatus::Active->value)->toBe('Active')
        ->and(SerialNoStatus::cases())->toHaveCount(4);
});
