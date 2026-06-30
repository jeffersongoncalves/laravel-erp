<?php

use JeffersonGoncalves\Erp\Hr\Models\Attendance;
use JeffersonGoncalves\Erp\Hr\Models\Employee;
use JeffersonGoncalves\Erp\Hr\Models\Holiday;
use JeffersonGoncalves\Erp\Hr\Models\HolidayList;
use JeffersonGoncalves\Erp\Hr\Models\LeaveApplication;
use JeffersonGoncalves\Erp\Hr\Models\LeaveType;
use JeffersonGoncalves\Erp\Hr\Models\PayrollEntry;
use JeffersonGoncalves\Erp\Hr\Models\SalaryComponent;
use JeffersonGoncalves\Erp\Hr\Models\SalarySlip;
use JeffersonGoncalves\Erp\Hr\Models\SalarySlipComponent;
use JeffersonGoncalves\Erp\Hr\Models\SalaryStructure;
use JeffersonGoncalves\Erp\Hr\Models\SalaryStructureAssignment;
use JeffersonGoncalves\Erp\Hr\Models\SalaryStructureComponent;

return [
    /*
    |--------------------------------------------------------------------------
    | Table Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix applied to all tables created by the package to avoid
    | collision with existing application tables.
    | Set to null to use table names without a prefix.
    |
    */
    'table_prefix' => 'erp_',

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | Models used by the package. Can be overridden to extend the default
    | behavior. Custom models must implement the corresponding contract
    | interface (see src/Models/Contracts/).
    |
    */
    'models' => [
        'employee' => Employee::class,
        'leave_type' => LeaveType::class,
        'holiday_list' => HolidayList::class,
        'holiday' => Holiday::class,
        'salary_component' => SalaryComponent::class,
        'salary_structure' => SalaryStructure::class,
        'salary_structure_component' => SalaryStructureComponent::class,
        'salary_structure_assignment' => SalaryStructureAssignment::class,
        'attendance' => Attendance::class,
        'leave_application' => LeaveApplication::class,
        'salary_slip' => SalarySlip::class,
        'salary_slip_component' => SalarySlipComponent::class,
        'payroll_entry' => PayrollEntry::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    |
    | Optional default HR settings. `default_payroll_payable_account` is the
    | accounting account a salary slip credits with its net pay when no payable
    | account is supplied to the posting service.
    |
    */
    'default_payroll_payable_account' => null,
];
