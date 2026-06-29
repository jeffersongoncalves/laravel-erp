<?php

namespace JeffersonGoncalves\Erp\Maintenance\Enums;

enum MaintenanceType: string
{
    case Scheduled = 'Scheduled';
    case Unscheduled = 'Unscheduled';
    case Breakdown = 'Breakdown';

    public function label(): string
    {
        return __('erp-maintenance::erp-maintenance.maintenance_type.'.$this->value);
    }
}
