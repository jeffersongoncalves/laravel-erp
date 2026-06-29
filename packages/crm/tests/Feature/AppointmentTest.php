<?php

use JeffersonGoncalves\Erp\Crm\Models\Appointment;

it('creates an appointment with the default status', function () {
    $appointment = Appointment::factory()->create();

    expect($appointment->status)->toBe('Open')
        ->and($appointment->scheduled_time)->not->toBeNull()
        ->and($appointment->company->id)->toBe($appointment->company_id);
});

it('casts the scheduled time to a datetime', function () {
    $appointment = Appointment::factory()->create([
        'scheduled_time' => '2026-01-15 14:30:00',
    ]);

    expect($appointment->scheduled_time->format('Y-m-d H:i'))->toBe('2026-01-15 14:30');
});
