<?php

use JeffersonGoncalves\Erp\Core\Models\NamingSeries;

it('uses the configured table prefix', function () {
    expect((new NamingSeries)->getTable())->toBe('erp_naming_series');
});

it('can be created and casts the counter to an integer', function () {
    $series = NamingSeries::create(['series' => 'SINV-2026-', 'current' => 5]);

    expect($series->series)->toBe('SINV-2026-')
        ->and($series->current)->toBe(5);
});
