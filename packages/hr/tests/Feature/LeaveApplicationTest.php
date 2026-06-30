<?php

use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Hr\Models\Employee;
use JeffersonGoncalves\Erp\Hr\Models\LeaveApplication;
use JeffersonGoncalves\Erp\Hr\Models\LeaveType;
use JeffersonGoncalves\Erp\Hr\Services\LeaveService;

it('decrements the applicable balance when an application is submitted', function () {
    $employee = Employee::factory()->create();
    $leaveType = LeaveType::factory()->create(['max_leaves_allowed' => 10, 'allow_negative' => false]);

    $service = app(LeaveService::class);

    expect($service->applicableBalance($employee, $leaveType))->toBe(10.0);

    $application = LeaveApplication::factory()->create([
        'employee_id' => $employee->id,
        'leave_type_id' => $leaveType->id,
        'total_leave_days' => 6,
    ]);

    $application->submit();

    expect($application->docstatus)->toBe(DocStatus::Submitted)
        ->and($service->applicableBalance($employee, $leaveType))->toBe(4.0);
});

it('throws when an application overdraws the leave balance', function () {
    $employee = Employee::factory()->create();
    $leaveType = LeaveType::factory()->create(['max_leaves_allowed' => 5, 'allow_negative' => false]);

    $application = LeaveApplication::factory()->create([
        'employee_id' => $employee->id,
        'leave_type_id' => $leaveType->id,
        'total_leave_days' => 8,
    ]);

    expect(fn () => $application->submit())->toThrow(DomainException::class);

    $application->refresh();

    expect($application->docstatus)->toBe(DocStatus::Draft);
});

it('allows an overdraw when the leave type permits a negative balance', function () {
    $employee = Employee::factory()->create();
    $leaveType = LeaveType::factory()->allowNegative()->create(['max_leaves_allowed' => 5]);

    $application = LeaveApplication::factory()->create([
        'employee_id' => $employee->id,
        'leave_type_id' => $leaveType->id,
        'total_leave_days' => 8,
    ]);

    $application->submit();

    expect($application->docstatus)->toBe(DocStatus::Submitted);
});
