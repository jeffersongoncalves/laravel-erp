<?php

use JeffersonGoncalves\Erp\Core\Models\FiscalYear;

it('uses the configured table prefix', function () {
    expect((new FiscalYear)->getTable())->toBe('erp_fiscal_years');
});

it('can be created via factory and casts dates', function () {
    $year = FiscalYear::factory()->create([
        'name' => '2026',
        'year_start_date' => '2026-01-01',
        'year_end_date' => '2026-12-31',
    ]);

    expect($year->name)->toBe('2026')
        ->and($year->year_start_date->format('Y-m-d'))->toBe('2026-01-01')
        ->and($year->is_short_year)->toBeFalse();
});
