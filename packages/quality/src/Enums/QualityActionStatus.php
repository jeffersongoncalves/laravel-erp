<?php

namespace JeffersonGoncalves\Erp\Quality\Enums;

enum QualityActionStatus: string
{
    case Open = 'Open';
    case InProgress = 'In Progress';
    case Completed = 'Completed';
    case Cancelled = 'Cancelled';

    public function label(): string
    {
        return __('erp-quality::erp-quality.quality_action_status.'.$this->value);
    }
}
