<?php

namespace JeffersonGoncalves\Erp\Crm\Enums;

enum LeadStatus: string
{
    case Lead = 'Lead';
    case Open = 'Open';
    case Replied = 'Replied';
    case Opportunity = 'Opportunity';
    case Quotation = 'Quotation';
    case Converted = 'Converted';
    case DoNotContact = 'Do Not Contact';

    public function label(): string
    {
        return __('erp-crm::erp-crm.lead_status.'.$this->value);
    }
}
