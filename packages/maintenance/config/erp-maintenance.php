<?php

use JeffersonGoncalves\Erp\Maintenance\Enums\MaintenanceType;
use JeffersonGoncalves\Erp\Maintenance\Models\MaintenanceSchedule;
use JeffersonGoncalves\Erp\Maintenance\Models\MaintenanceScheduleDetail;
use JeffersonGoncalves\Erp\Maintenance\Models\MaintenanceScheduleItem;
use JeffersonGoncalves\Erp\Maintenance\Models\MaintenanceVisit;
use JeffersonGoncalves\Erp\Maintenance\Models\MaintenanceVisitPurpose;

return [
    /*
    |--------------------------------------------------------------------------
    | Table Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix applied to all tables created by the package to avoid
    | collision with existing application tables.
    | Set to null to use table names without a prefix.
    |
    */
    'table_prefix' => 'erp_',

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | Models used by the package. Can be overridden to extend the default
    | behavior.
    |
    */
    'models' => [
        'maintenance_schedule' => MaintenanceSchedule::class,
        'maintenance_schedule_item' => MaintenanceScheduleItem::class,
        'maintenance_schedule_detail' => MaintenanceScheduleDetail::class,
        'maintenance_visit' => MaintenanceVisit::class,
        'maintenance_visit_purpose' => MaintenanceVisitPurpose::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    |
    | Optional default maintenance settings. `default_maintenance_type` is
    | applied to new maintenance visits and `default_periodicity` is the
    | cadence used when generating dated visits for a maintenance schedule.
    |
    */
    'default_maintenance_type' => MaintenanceType::Scheduled->value,

    'default_periodicity' => 'Monthly',
];
