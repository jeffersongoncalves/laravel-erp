<?php

namespace JeffersonGoncalves\Erp\Selling\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Selling\Models\CouponCode;

/** @extends Factory<CouponCode> */
class CouponCodeFactory extends Factory
{
    protected $model = CouponCode::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'coupon_name' => fake()->unique()->words(2, true),
            'coupon_code' => fake()->unique()->bothify('COUPON-####'),
            'maximum_use' => 0,
            'used' => 0,
        ];
    }
}
