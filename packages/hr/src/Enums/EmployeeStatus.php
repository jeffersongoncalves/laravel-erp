<?php

namespace JeffersonGoncalves\Erp\Hr\Enums;

enum EmployeeStatus: string
{
    case Active = 'Active';
    case Inactive = 'Inactive';
    case Left = 'Left';

    public function label(): string
    {
        return __('erp-hr::erp-hr.employee_status.'.$this->value);
    }
}
