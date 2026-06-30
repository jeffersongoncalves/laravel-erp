<?php

namespace JeffersonGoncalves\Erp\Hr\Enums;

enum LeaveApplicationStatus: string
{
    case Open = 'Open';
    case Approved = 'Approved';
    case Rejected = 'Rejected';

    public function label(): string
    {
        return __('erp-hr::erp-hr.leave_status.'.$this->value);
    }
}
