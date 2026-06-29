<?php

use JeffersonGoncalves\Erp\Projects\Models\ActivityType;
use JeffersonGoncalves\Erp\Projects\Models\Project;
use JeffersonGoncalves\Erp\Projects\Models\Task;
use JeffersonGoncalves\Erp\Projects\Models\Timesheet;
use JeffersonGoncalves\Erp\Projects\Models\TimesheetDetail;

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
    | behavior. Custom models must implement the corresponding contract
    | interface (see src/Models/Contracts/).
    |
    */
    'models' => [
        'activity_type' => ActivityType::class,
        'project' => Project::class,
        'task' => Task::class,
        'timesheet' => Timesheet::class,
        'timesheet_detail' => TimesheetDetail::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    |
    | Optional default projects settings. `default_activity_type` references an
    | activity type applied to timesheet lines that leave it blank, and
    | `default_party_type` is the party kind stored on new projects.
    |
    */
    'default_activity_type' => null,

    'default_party_type' => 'Customer',
];
