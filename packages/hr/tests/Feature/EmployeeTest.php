<?php

use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Core\Models\Department;
use JeffersonGoncalves\Erp\Core\Models\Designation;
use JeffersonGoncalves\Erp\Hr\Enums\EmployeeStatus;
use JeffersonGoncalves\Erp\Hr\Enums\Gender;
use JeffersonGoncalves\Erp\Hr\Models\Employee;

it('creates an employee with default attributes', function () {
    $company = Company::factory()->create();

    $employee = Employee::factory()->create([
        'company_id' => $company->id,
        'employee_number' => 'EMP-0001',
        'first_name' => 'Ada',
        'last_name' => 'Lovelace',
        'gender' => Gender::Female,
    ]);

    expect($employee->employee_number)->toBe('EMP-0001')
        ->and($employee->first_name)->toBe('Ada')
        ->and($employee->status)->toBe(EmployeeStatus::Active)
        ->and($employee->gender)->toBe(Gender::Female)
        ->and($employee->company->id)->toBe($company->id);
});

it('links an employee to a department and designation', function () {
    $department = Department::factory()->create();
    $designation = Designation::factory()->create();

    $employee = Employee::factory()->create([
        'department_id' => $department->id,
        'designation_id' => $designation->id,
    ]);

    expect($employee->department->id)->toBe($department->id)
        ->and($employee->designation->id)->toBe($designation->id);
});

it('scopes active employees', function () {
    Employee::factory()->create();
    Employee::factory()->left()->create();

    expect(Employee::query()->active()->count())->toBe(1);
});
