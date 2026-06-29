<?php

use JeffersonGoncalves\Erp\Buying\Enums\PurchaseOrderStatus;
use JeffersonGoncalves\Erp\Buying\Enums\RfqStatus;

it('exposes the purchase order statuses', function () {
    expect(PurchaseOrderStatus::Draft->value)->toBe('Draft')
        ->and(PurchaseOrderStatus::ToReceiveAndBill->value)->toBe('To Receive and Bill')
        ->and(PurchaseOrderStatus::ToReceive->value)->toBe('To Receive')
        ->and(PurchaseOrderStatus::ToBill->value)->toBe('To Bill')
        ->and(PurchaseOrderStatus::Completed->value)->toBe('Completed')
        ->and(PurchaseOrderStatus::Cancelled->value)->toBe('Cancelled')
        ->and(PurchaseOrderStatus::Closed->value)->toBe('Closed')
        ->and(PurchaseOrderStatus::cases())->toHaveCount(7);
});

it('exposes the rfq statuses', function () {
    expect(RfqStatus::Draft->value)->toBe('Draft')
        ->and(RfqStatus::Submitted->value)->toBe('Submitted')
        ->and(RfqStatus::Cancelled->value)->toBe('Cancelled')
        ->and(RfqStatus::cases())->toHaveCount(3);
});

it('resolves enum labels through translations', function () {
    expect(PurchaseOrderStatus::Draft->label())->toBe('Draft')
        ->and(RfqStatus::Submitted->label())->toBe('Submitted');
});
