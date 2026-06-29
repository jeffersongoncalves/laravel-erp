<?php

use JeffersonGoncalves\Erp\Crm\Models\Appointment;
use JeffersonGoncalves\Erp\Crm\Models\Campaign;
use JeffersonGoncalves\Erp\Crm\Models\Contract;
use JeffersonGoncalves\Erp\Crm\Models\Lead;
use JeffersonGoncalves\Erp\Crm\Models\LeadSource;
use JeffersonGoncalves\Erp\Crm\Models\Opportunity;
use JeffersonGoncalves\Erp\Crm\Models\OpportunityItem;

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
        'lead_source' => LeadSource::class,
        'campaign' => Campaign::class,
        'contract' => Contract::class,
        'lead' => Lead::class,
        'opportunity' => Opportunity::class,
        'opportunity_item' => OpportunityItem::class,
        'appointment' => Appointment::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    |
    | Optional default CRM settings applied when converting a lead into a
    | selling customer. `default_customer_group` references a selling customer
    | group and `default_currency` the currency assigned to the new customer.
    |
    */
    'default_customer_group' => null,

    'default_currency' => 'USD',
];
