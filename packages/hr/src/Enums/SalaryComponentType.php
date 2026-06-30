<?php

namespace JeffersonGoncalves\Erp\Hr\Enums;

enum SalaryComponentType: string
{
    case Earning = 'Earning';
    case Deduction = 'Deduction';

    public function label(): string
    {
        return __('erp-hr::erp-hr.salary_component_type.'.$this->value);
    }
}
