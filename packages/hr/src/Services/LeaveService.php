<?php

namespace JeffersonGoncalves\Erp\Hr\Services;

use DomainException;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Hr\Models\Employee;
use JeffersonGoncalves\Erp\Hr\Models\LeaveApplication;
use JeffersonGoncalves\Erp\Hr\Models\LeaveType;
use JeffersonGoncalves\Erp\Hr\Support\ModelResolver;

/**
 * Tracks leave balances and guards leave applications against overdrawing the
 * allowance configured on their leave type.
 */
class LeaveService
{
    /**
     * The remaining leave balance for an employee and leave type: the type's
     * allowance minus the days already taken by submitted applications.
     */
    public function applicableBalance(Employee $employee, LeaveType $leaveType): float
    {
        $taken = (float) ModelResolver::leaveApplication()::query()
            ->where('employee_id', $employee->getKey())
            ->where('leave_type_id', $leaveType->getKey())
            ->where('docstatus', DocStatus::Submitted->value)
            ->sum('total_leave_days');

        return (float) $leaveType->max_leaves_allowed - $taken;
    }

    /**
     * Ensure submitting an application would not overdraw the leave balance,
     * unless the leave type explicitly allows a negative balance.
     *
     * @throws DomainException when the requested days exceed the balance.
     */
    public function guardBalance(LeaveApplication $application): void
    {
        $leaveType = $application->leaveType;
        $employee = $application->employee;

        if ($leaveType === null || $employee === null) {
            return;
        }

        if ($leaveType->allow_negative) {
            return;
        }

        $balance = $this->applicableBalance($employee, $leaveType);

        if ($balance - (float) $application->total_leave_days < 0) {
            throw new DomainException('Leave balance exceeded for the requested period');
        }
    }
}
