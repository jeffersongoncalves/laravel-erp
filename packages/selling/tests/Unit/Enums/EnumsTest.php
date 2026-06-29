<?php

use JeffersonGoncalves\Erp\Selling\Enums\QuotationStatus;
use JeffersonGoncalves\Erp\Selling\Enums\SalesOrderStatus;

it('exposes the quotation statuses', function () {
    expect(QuotationStatus::cases())->toHaveCount(6)
        ->and(QuotationStatus::Ordered->value)->toBe('Ordered')
        ->and(QuotationStatus::Draft->value)->toBe('Draft');
});

it('exposes the sales order statuses', function () {
    expect(SalesOrderStatus::cases())->toHaveCount(7)
        ->and(SalesOrderStatus::ToDeliverAndBill->value)->toBe('To Deliver and Bill')
        ->and(SalesOrderStatus::Completed->value)->toBe('Completed');
});
