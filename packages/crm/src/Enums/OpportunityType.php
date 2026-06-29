<?php

namespace JeffersonGoncalves\Erp\Crm\Enums;

enum OpportunityType: string
{
    case Sales = 'Sales';
    case Support = 'Support';
    case Maintenance = 'Maintenance';

    public function label(): string
    {
        return __('erp-crm::erp-crm.opportunity_type.'.$this->value);
    }
}
