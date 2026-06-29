<?php

namespace JeffersonGoncalves\Erp\Support\Enums;

enum IssuePriority: string
{
    case Low = 'Low';
    case Medium = 'Medium';
    case High = 'High';
    case Urgent = 'Urgent';

    public function label(): string
    {
        return __('erp-support::erp-support.issue_priority.'.$this->value);
    }
}
