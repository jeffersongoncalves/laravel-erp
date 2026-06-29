<?php

use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Projects\Models\Timesheet;

it('recomputes totals from its detail rows while draft', function () {
    $timesheet = Timesheet::factory()->create();

    $timesheet->details()->create([
        'hours' => 4,
        'is_billable' => true,
        'billing_rate' => 100,
        'costing_rate' => 40,
    ]);

    $timesheet->details()->create([
        'hours' => 2,
        'is_billable' => false,
        'billing_rate' => 100,
        'costing_rate' => 40,
    ]);

    $timesheet->refresh();

    expect($timesheet->total_hours)->toBe(6.0)
        ->and($timesheet->total_billable_hours)->toBe(4.0)
        ->and($timesheet->total_billable_amount)->toBe(400.0)
        ->and($timesheet->total_costing_amount)->toBe(240.0);
});

it('computes the billing and costing amounts on each detail row', function () {
    $timesheet = Timesheet::factory()->create();

    $detail = $timesheet->details()->create([
        'hours' => 3,
        'is_billable' => true,
        'billing_rate' => 80,
        'costing_rate' => 30,
    ]);

    expect($detail->billing_amount)->toBe(240.0)
        ->and($detail->costing_amount)->toBe(90.0);
});

it('does not bill a non-billable detail row', function () {
    $timesheet = Timesheet::factory()->create();

    $detail = $timesheet->details()->create([
        'hours' => 3,
        'is_billable' => false,
        'billing_rate' => 80,
        'costing_rate' => 30,
    ]);

    expect($detail->billing_amount)->toBe(0.0)
        ->and($detail->costing_amount)->toBe(90.0);
});

it('submits a timesheet', function () {
    $timesheet = Timesheet::factory()->create();
    $timesheet->details()->create(['hours' => 4, 'billing_rate' => 100, 'costing_rate' => 40]);
    $timesheet->refresh();

    $timesheet->submit();

    expect($timesheet->docstatus)->toBe(DocStatus::Submitted)
        ->and($timesheet->isSubmitted())->toBeTrue()
        ->and($timesheet->total_billable_amount)->toBe(400.0);
});
