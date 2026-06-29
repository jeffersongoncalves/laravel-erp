<?php

namespace JeffersonGoncalves\Erp\Maintenance\Enums;

enum MaintenanceVisitPurposeType: string
{
    case Maintenance = 'Maintenance';
    case Service = 'Service';
    case Installation = 'Installation';
    case Repair = 'Repair';

    public function label(): string
    {
        return __('erp-maintenance::erp-maintenance.maintenance_visit_purpose_type.'.$this->value);
    }
}
