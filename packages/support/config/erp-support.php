<?php

use JeffersonGoncalves\Erp\Support\Enums\IssuePriority;
use JeffersonGoncalves\Erp\Support\Models\Issue;
use JeffersonGoncalves\Erp\Support\Models\IssueType;
use JeffersonGoncalves\Erp\Support\Models\ServiceLevelAgreement;
use JeffersonGoncalves\Erp\Support\Models\ServiceLevelPriority;
use JeffersonGoncalves\Erp\Support\Models\WarrantyClaim;

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
        'issue_type' => IssueType::class,
        'service_level_agreement' => ServiceLevelAgreement::class,
        'service_level_priority' => ServiceLevelPriority::class,
        'issue' => Issue::class,
        'warranty_claim' => WarrantyClaim::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    |
    | Optional default support settings. `default_party_type` is the party
    | linked to issues and warranty claims (a dynamic link, e.g. a selling
    | customer), and `default_priority` is applied to new issues.
    |
    */
    'default_party_type' => 'Customer',

    'default_priority' => IssuePriority::Medium->value,
];
