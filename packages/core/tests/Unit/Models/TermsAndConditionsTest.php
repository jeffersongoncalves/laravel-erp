<?php

use JeffersonGoncalves\Erp\Core\Models\TermsAndConditions;

it('uses the configured table prefix', function () {
    expect((new TermsAndConditions)->getTable())->toBe('erp_terms_and_conditions');
});

it('can be created via factory', function () {
    $terms = TermsAndConditions::factory()->create([
        'name' => 'Default Terms',
        'content' => 'Payment due in 30 days.',
    ]);

    expect($terms->name)->toBe('Default Terms')
        ->and($terms->content)->toBe('Payment due in 30 days.');
});
