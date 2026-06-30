<?php

namespace JeffersonGoncalves\Erp\Hr\Enums;

enum Gender: string
{
    case Male = 'Male';
    case Female = 'Female';
    case Other = 'Other';

    public function label(): string
    {
        return __('erp-hr::erp-hr.gender.'.$this->value);
    }
}
