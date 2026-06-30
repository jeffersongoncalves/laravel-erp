<?php

namespace JeffersonGoncalves\Erp\Selling\Services;

use DomainException;
use JeffersonGoncalves\Erp\Selling\Models\CouponCode;

/**
 * Validates and redeems coupon codes against their validity window and usage cap.
 */
class CouponCodeService
{
    /**
     * A coupon is valid when $date falls inside its validity window and it has
     * remaining uses. A maximum_use of 0 means the coupon is unlimited.
     */
    public function isValid(CouponCode $coupon, string $date): bool
    {
        if ($coupon->valid_from !== null && $date < $coupon->valid_from->toDateString()) {
            return false;
        }

        if ($coupon->valid_upto !== null && $date > $coupon->valid_upto->toDateString()) {
            return false;
        }

        return $coupon->maximum_use === 0 || $coupon->used < $coupon->maximum_use;
    }

    /**
     * Redeem one use of the coupon for today.
     *
     * @throws DomainException when the coupon is outside its validity window or
     *                         has reached its usage cap.
     */
    public function redeem(CouponCode $coupon): void
    {
        if (! $this->isValid($coupon, now()->toDateString())) {
            throw new DomainException('Coupon code is not valid');
        }

        $coupon->increment('used');
    }
}
