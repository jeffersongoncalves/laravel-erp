<?php

use JeffersonGoncalves\Erp\Selling\Enums\PricingRuleRateOrDiscount;
use JeffersonGoncalves\Erp\Selling\Models\PricingRule;
use JeffersonGoncalves\Erp\Selling\Services\PricingRuleService;

it('resolves the highest-priority rule matching item, quantity and date', function () {
    $low = PricingRule::factory()->create(['priority' => 1, 'min_qty' => 1, 'max_qty' => 0]);
    $low->items()->create(['item_code' => 'WIDGET-1']);

    $high = PricingRule::factory()->create(['priority' => 5, 'min_qty' => 1, 'max_qty' => 0]);
    $high->items()->create(['item_code' => 'WIDGET-1']);

    $resolved = app(PricingRuleService::class)->resolve('WIDGET-1', 3, '2026-06-30');

    expect($resolved)->not->toBeNull()
        ->and($resolved->id)->toBe($high->id);
});

it('does not resolve rules outside the quantity band, validity window, or when disabled', function () {
    $service = app(PricingRuleService::class);

    $belowBand = PricingRule::factory()->create(['min_qty' => 10, 'max_qty' => 0]);
    $belowBand->items()->create(['item_code' => 'WIDGET-1']);
    expect($service->resolve('WIDGET-1', 5, '2026-06-30'))->toBeNull();

    $aboveBand = PricingRule::factory()->create(['min_qty' => 1, 'max_qty' => 4]);
    $aboveBand->items()->create(['item_code' => 'GADGET-1']);
    expect($service->resolve('GADGET-1', 5, '2026-06-30'))->toBeNull();

    $expired = PricingRule::factory()->create(['min_qty' => 1, 'valid_from' => '2026-01-01', 'valid_upto' => '2026-03-01']);
    $expired->items()->create(['item_code' => 'OLD-1']);
    expect($service->resolve('OLD-1', 2, '2026-06-30'))->toBeNull();

    $disabled = PricingRule::factory()->disabled()->create(['min_qty' => 1]);
    $disabled->items()->create(['item_code' => 'OFF-1']);
    expect($service->resolve('OFF-1', 2, '2026-06-30'))->toBeNull();
});

it('applies a flat rate', function () {
    $rule = PricingRule::factory()->create([
        'rate_or_discount' => PricingRuleRateOrDiscount::Rate->value,
        'rate' => 80,
    ]);

    expect(app(PricingRuleService::class)->applyTo($rule, 100))->toBe(80.0);
});

it('applies a percentage discount', function () {
    $rule = PricingRule::factory()->create([
        'rate_or_discount' => PricingRuleRateOrDiscount::DiscountPercentage->value,
        'discount_percentage' => 10,
    ]);

    expect(app(PricingRuleService::class)->applyTo($rule, 100))->toBe(90.0);
});

it('applies a fixed amount discount', function () {
    $rule = PricingRule::factory()->create([
        'rate_or_discount' => PricingRuleRateOrDiscount::DiscountAmount->value,
        'discount_amount' => 15,
    ]);

    expect(app(PricingRuleService::class)->applyTo($rule, 100))->toBe(85.0);
});
