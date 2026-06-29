<?php

namespace JeffersonGoncalves\Erp\Crm\Enums;

enum OpportunityStatus: string
{
    case Open = 'Open';
    case Quotation = 'Quotation';
    case Converted = 'Converted';
    case Lost = 'Lost';
    case Closed = 'Closed';
    case Replied = 'Replied';

    public function label(): string
    {
        return __('erp-crm::erp-crm.opportunity_status.'.$this->value);
    }
}
