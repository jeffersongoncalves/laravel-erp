<?php

namespace JeffersonGoncalves\Erp\Hr\Enums;

enum AttendanceStatus: string
{
    case Present = 'Present';
    case Absent = 'Absent';
    case HalfDay = 'Half Day';
    case OnLeave = 'On Leave';

    public function label(): string
    {
        return __('erp-hr::erp-hr.attendance_status.'.$this->value);
    }
}
