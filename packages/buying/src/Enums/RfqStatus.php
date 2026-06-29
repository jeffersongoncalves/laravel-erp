<?php

namespace JeffersonGoncalves\Erp\Buying\Enums;

enum RfqStatus: string
{
    case Draft = 'Draft';
    case Submitted = 'Submitted';
    case Cancelled = 'Cancelled';

    public function label(): string
    {
        return __('erp-buying::erp-buying.rfq_status.'.$this->value);
    }
}
