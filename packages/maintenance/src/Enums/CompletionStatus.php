<?php

namespace JeffersonGoncalves\Erp\Maintenance\Enums;

enum CompletionStatus: string
{
    case Pending = 'Pending';
    case PartiallyCompleted = 'Partially Completed';
    case FullyCompleted = 'Fully Completed';

    public function label(): string
    {
        return __('erp-maintenance::erp-maintenance.completion_status.'.$this->value);
    }
}
