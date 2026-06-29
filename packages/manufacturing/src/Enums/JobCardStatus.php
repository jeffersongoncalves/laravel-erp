<?php

namespace JeffersonGoncalves\Erp\Manufacturing\Enums;

enum JobCardStatus: string
{
    case Open = 'Open';
    case WorkInProgress = 'Work In Progress';
    case Completed = 'Completed';
    case OnHold = 'On Hold';
    case Cancelled = 'Cancelled';

    public function label(): string
    {
        return __('erp-manufacturing::erp-manufacturing.job_card_status.'.$this->value);
    }
}
