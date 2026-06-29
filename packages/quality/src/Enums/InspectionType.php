<?php

namespace JeffersonGoncalves\Erp\Quality\Enums;

enum InspectionType: string
{
    case Incoming = 'Incoming';
    case Outgoing = 'Outgoing';
    case InProcess = 'In Process';

    public function label(): string
    {
        return __('erp-quality::erp-quality.inspection_type.'.$this->value);
    }
}
