<?php

use JeffersonGoncalves\Erp\Selling\Models\LoyaltyProgram;
use JeffersonGoncalves\Erp\Selling\Services\LoyaltyProgramService;

it('returns the highest tier whose minimum spend is reached', function () {
    $program = LoyaltyProgram::factory()->create();
    $program->tiers()->create(['tier_name' => 'Silver', 'min_spent' => 0, 'collection_factor' => 1]);
    $gold = $program->tiers()->create(['tier_name' => 'Gold', 'min_spent' => 1000, 'collection_factor' => 2]);
    $program->tiers()->create(['tier_name' => 'Platinum', 'min_spent' => 5000, 'collection_factor' => 3]);

    $tier = app(LoyaltyProgramService::class)->tierFor($program, 2000);

    expect($tier)->not->toBeNull()
        ->and($tier->id)->toBe($gold->id);
});

it('returns null when the spend reaches no tier', function () {
    $program = LoyaltyProgram::factory()->create();
    $program->tiers()->create(['tier_name' => 'Gold', 'min_spent' => 1000, 'collection_factor' => 2]);

    expect(app(LoyaltyProgramService::class)->tierFor($program, 500))->toBeNull();
});

it('awards floored points using the matched tier collection factor', function () {
    $program = LoyaltyProgram::factory()->create();
    $program->tiers()->create(['tier_name' => 'Gold', 'min_spent' => 1000, 'collection_factor' => 1.5]);

    // 2001 * 1.5 = 3001.5 -> floor 3001
    expect(app(LoyaltyProgramService::class)->awardPoints($program, 2001))->toBe(3001);
});

it('awards zero points when no tier qualifies', function () {
    $program = LoyaltyProgram::factory()->create();
    $program->tiers()->create(['tier_name' => 'Gold', 'min_spent' => 1000, 'collection_factor' => 2]);

    expect(app(LoyaltyProgramService::class)->awardPoints($program, 500))->toBe(0);
});
