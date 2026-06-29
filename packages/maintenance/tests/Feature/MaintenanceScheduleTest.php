<?php

use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Maintenance\Enums\CompletionStatus;
use JeffersonGoncalves\Erp\Maintenance\Models\MaintenanceSchedule;
use JeffersonGoncalves\Erp\Maintenance\Services\MaintenanceScheduleService;

it('creates a maintenance schedule with items and defaults', function () {
    $schedule = MaintenanceSchedule::factory()->create();

    $schedule->items()->create(['item_code' => 'PUMP-01']);

    $schedule->refresh();

    expect($schedule->party_type)->toBe('Customer')
        ->and($schedule->status)->toBe('Draft')
        ->and($schedule->docstatus)->toBe(DocStatus::Draft)
        ->and($schedule->company->id)->toBe($schedule->company_id)
        ->and($schedule->items)->toHaveCount(1)
        ->and($schedule->items->first()->periodicity)->toBe('Monthly')
        ->and($schedule->items->first()->no_of_visits)->toBe(1)
        ->and($schedule->items->first()->maintenanceSchedule->id)->toBe($schedule->id);
});

it('generates dated visit rows on submit stepping monthly', function () {
    $schedule = MaintenanceSchedule::factory()->create(['transaction_date' => '2026-01-01']);

    $schedule->items()->create([
        'item_code' => 'PUMP-01',
        'start_date' => '2026-01-01',
        'periodicity' => 'Monthly',
        'no_of_visits' => 3,
    ]);

    $schedule->submit();
    $schedule->refresh();

    expect($schedule->docstatus)->toBe(DocStatus::Submitted)
        ->and($schedule->scheduleDetails)->toHaveCount(3);

    $dates = $schedule->scheduleDetails()->orderBy('scheduled_date')->get();

    expect($dates[0]->scheduled_date->toDateString())->toBe('2026-01-01')
        ->and($dates[1]->scheduled_date->toDateString())->toBe('2026-02-01')
        ->and($dates[2]->scheduled_date->toDateString())->toBe('2026-03-01')
        ->and($dates[0]->completion_status)->toBe(CompletionStatus::Pending)
        ->and($dates[0]->item_code)->toBe('PUMP-01');
});

it('steps weekly when generating the schedule directly', function () {
    $schedule = MaintenanceSchedule::factory()->create();

    $schedule->items()->create([
        'item_code' => 'AC-01',
        'start_date' => '2026-01-01',
        'periodicity' => 'Weekly',
        'no_of_visits' => 2,
        'serial_no' => 'SN-001',
    ]);

    app(MaintenanceScheduleService::class)->generateSchedule($schedule);

    $dates = $schedule->scheduleDetails()->orderBy('scheduled_date')->get();

    expect($dates)->toHaveCount(2)
        ->and($dates[0]->scheduled_date->toDateString())->toBe('2026-01-01')
        ->and($dates[1]->scheduled_date->toDateString())->toBe('2026-01-08')
        ->and($dates[0]->serial_no)->toBe('SN-001');
});

it('steps quarterly and yearly when generating the schedule', function () {
    $schedule = MaintenanceSchedule::factory()->create();

    $schedule->items()->create([
        'item_code' => 'Q-01',
        'start_date' => '2026-01-01',
        'periodicity' => 'Quarterly',
        'no_of_visits' => 2,
    ]);
    $schedule->items()->create([
        'item_code' => 'Y-01',
        'start_date' => '2026-01-01',
        'periodicity' => 'Yearly',
        'no_of_visits' => 2,
    ]);

    app(MaintenanceScheduleService::class)->generateSchedule($schedule);

    $quarterly = $schedule->scheduleDetails()->where('item_code', 'Q-01')->orderBy('scheduled_date')->get();
    $yearly = $schedule->scheduleDetails()->where('item_code', 'Y-01')->orderBy('scheduled_date')->get();

    expect($quarterly[1]->scheduled_date->toDateString())->toBe('2026-04-01')
        ->and($yearly[1]->scheduled_date->toDateString())->toBe('2027-01-01');
});
