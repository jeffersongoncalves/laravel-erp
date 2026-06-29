<?php

namespace JeffersonGoncalves\Erp\Projects\Enums;

enum TaskStatus: string
{
    case Open = 'Open';
    case Working = 'Working';
    case PendingReview = 'Pending Review';
    case Overdue = 'Overdue';
    case Completed = 'Completed';
    case Cancelled = 'Cancelled';

    public function label(): string
    {
        return __('erp-projects::erp-projects.task_status.'.$this->value);
    }
}
