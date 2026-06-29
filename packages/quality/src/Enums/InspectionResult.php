<?php

namespace JeffersonGoncalves\Erp\Quality\Enums;

enum InspectionResult: string
{
    case Accepted = 'Accepted';
    case Rejected = 'Rejected';

    public function label(): string
    {
        return __('erp-quality::erp-quality.inspection_result.'.$this->value);
    }
}
