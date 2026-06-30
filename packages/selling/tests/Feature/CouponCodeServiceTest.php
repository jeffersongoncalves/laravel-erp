<?php

use JeffersonGoncalves\Erp\Selling\Models\CouponCode;
use JeffersonGoncalves\Erp\Selling\Services\CouponCodeService;

it('treats a coupon within its window and usage cap as valid', function () {
    $coupon = CouponCode::factory()->create([
        'valid_from' => '2026-01-01',
        'valid_upto' => '2026-12-31',
        'maximum_use' => 2,
        'used' => 0,
    ]);

    expect(app(CouponCodeService::class)->isValid($coupon, '2026-06-30'))->toBeTrue();
});

it('treats a coupon outside its window as invalid', function () {
    $coupon = CouponCode::factory()->create([
        'valid_from' => '2026-01-01',
        'valid_upto' => '2026-03-01',
        'maximum_use' => 0,
    ]);

    expect(app(CouponCodeService::class)->isValid($coupon, '2026-06-30'))->toBeFalse();
});

it('redeems a coupon and increments its used count', function () {
    $coupon = CouponCode::factory()->create(['maximum_use' => 2, 'used' => 0]);

    app(CouponCodeService::class)->redeem($coupon);

    expect($coupon->fresh()->used)->toBe(1);
});

it('throws when redeeming a fully used coupon', function () {
    $coupon = CouponCode::factory()->create(['maximum_use' => 1, 'used' => 1]);

    expect(fn () => app(CouponCodeService::class)->redeem($coupon))
        ->toThrow(DomainException::class, 'Coupon code is not valid');
});
