<?php

namespace JeffersonGoncalves\Erp\Projects\Enums;

enum TaskPriority: string
{
    case Low = 'Low';
    case Medium = 'Medium';
    case High = 'High';
    case Urgent = 'Urgent';

    public function label(): string
    {
        return __('erp-projects::erp-projects.task_priority.'.$this->value);
    }
}
