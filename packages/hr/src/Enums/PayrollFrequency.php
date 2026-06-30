<?php

namespace JeffersonGoncalves\Erp\Hr\Enums;

enum PayrollFrequency: string
{
    case Monthly = 'Monthly';
    case Weekly = 'Weekly';
    case Biweekly = 'Biweekly';
    case Daily = 'Daily';

    public function label(): string
    {
        return __('erp-hr::erp-hr.payroll_frequency.'.$this->value);
    }
}
