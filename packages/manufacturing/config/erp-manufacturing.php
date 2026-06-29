<?php

use JeffersonGoncalves\Erp\Manufacturing\Models\Bom;
use JeffersonGoncalves\Erp\Manufacturing\Models\BomItem;
use JeffersonGoncalves\Erp\Manufacturing\Models\BomOperation;
use JeffersonGoncalves\Erp\Manufacturing\Models\JobCard;
use JeffersonGoncalves\Erp\Manufacturing\Models\JobCardTimeLog;
use JeffersonGoncalves\Erp\Manufacturing\Models\Operation;
use JeffersonGoncalves\Erp\Manufacturing\Models\Routing;
use JeffersonGoncalves\Erp\Manufacturing\Models\RoutingOperation;
use JeffersonGoncalves\Erp\Manufacturing\Models\WorkOrder;
use JeffersonGoncalves\Erp\Manufacturing\Models\WorkOrderItem;
use JeffersonGoncalves\Erp\Manufacturing\Models\Workstation;

return [
    /*
    |--------------------------------------------------------------------------
    | Table Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix applied to all tables created by the package. This is shared with
    | laravel-erp-core, laravel-erp-accounting and laravel-erp-stock so that
    | foreign keys across the ERP ecosystem resolve against a single set of
    | prefixed tables. Set to null to disable.
    |
    */
    'table_prefix' => 'erp_',

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | Models used by the package. Can be overridden to extend the default
    | behavior via the ModelResolver pattern.
    |
    */
    'models' => [
        'workstation' => Workstation::class,
        'operation' => Operation::class,
        'bom' => Bom::class,
        'bom_item' => BomItem::class,
        'bom_operation' => BomOperation::class,
        'routing' => Routing::class,
        'routing_operation' => RoutingOperation::class,
        'work_order' => WorkOrder::class,
        'work_order_item' => WorkOrderItem::class,
        'job_card' => JobCard::class,
        'job_card_time_log' => JobCardTimeLog::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | Default warehouses used by the manufacturing engine when a work order
    | does not declare its own. The work-in-progress (WIP) warehouse is where
    | raw materials are consumed from during production; the finished-goods
    | (FG) warehouse is where the produced item is received into.
    |
    */
    'default_wip_warehouse' => null,

    'default_fg_warehouse' => null,
];
