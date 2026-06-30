<?php

use JeffersonGoncalves\Erp\Hr\Enums\AttendanceStatus;
use JeffersonGoncalves\Erp\Hr\Enums\EmployeeStatus;
use JeffersonGoncalves\Erp\Hr\Enums\Gender;
use JeffersonGoncalves\Erp\Hr\Enums\LeaveApplicationStatus;
use JeffersonGoncalves\Erp\Hr\Enums\PayrollFrequency;
use JeffersonGoncalves\Erp\Hr\Enums\SalaryComponentType;

it('exposes the genders', function () {
    expect(Gender::cases())->toHaveCount(3)
        ->and(Gender::Male->value)->toBe('Male')
        ->and(Gender::Female->value)->toBe('Female');
});

it('exposes the employee statuses', function () {
    expect(EmployeeStatus::cases())->toHaveCount(3)
        ->and(EmployeeStatus::Active->value)->toBe('Active')
        ->and(EmployeeStatus::Left->value)->toBe('Left');
});

it('exposes the attendance statuses', function () {
    expect(AttendanceStatus::cases())->toHaveCount(4)
        ->and(AttendanceStatus::HalfDay->value)->toBe('Half Day')
        ->and(AttendanceStatus::OnLeave->value)->toBe('On Leave');
});

it('exposes the salary component types', function () {
    expect(SalaryComponentType::cases())->toHaveCount(2)
        ->and(SalaryComponentType::Earning->value)->toBe('Earning')
        ->and(SalaryComponentType::Deduction->value)->toBe('Deduction');
});

it('exposes the payroll frequencies', function () {
    expect(PayrollFrequency::cases())->toHaveCount(4)
        ->and(PayrollFrequency::Monthly->value)->toBe('Monthly')
        ->and(PayrollFrequency::Biweekly->value)->toBe('Biweekly');
});

it('exposes the leave application statuses', function () {
    expect(LeaveApplicationStatus::cases())->toHaveCount(3)
        ->and(LeaveApplicationStatus::Open->value)->toBe('Open')
        ->and(LeaveApplicationStatus::Approved->value)->toBe('Approved');
});
