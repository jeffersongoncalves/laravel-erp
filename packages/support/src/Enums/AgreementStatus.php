<?php

namespace JeffersonGoncalves\Erp\Support\Enums;

enum AgreementStatus: string
{
    case FirstResponseDue = 'First Response Due';
    case ResolutionDue = 'Resolution Due';
    case Fulfilled = 'Fulfilled';
    case Failed = 'Failed';
    case Paused = 'Paused';

    public function label(): string
    {
        return __('erp-support::erp-support.agreement_status.'.$this->value);
    }
}
