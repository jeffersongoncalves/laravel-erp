<?php

use JeffersonGoncalves\Erp\Quality\Enums\InspectionType;
use JeffersonGoncalves\Erp\Quality\Models\NonConformance;
use JeffersonGoncalves\Erp\Quality\Models\QualityAction;
use JeffersonGoncalves\Erp\Quality\Models\QualityActionResolution;
use JeffersonGoncalves\Erp\Quality\Models\QualityGoal;
use JeffersonGoncalves\Erp\Quality\Models\QualityGoalObjective;
use JeffersonGoncalves\Erp\Quality\Models\QualityInspection;
use JeffersonGoncalves\Erp\Quality\Models\QualityInspectionReading;
use JeffersonGoncalves\Erp\Quality\Models\QualityInspectionTemplate;
use JeffersonGoncalves\Erp\Quality\Models\QualityInspectionTemplateParameter;
use JeffersonGoncalves\Erp\Quality\Models\QualityProcedure;
use JeffersonGoncalves\Erp\Quality\Models\QualityProcedureProcess;
use JeffersonGoncalves\Erp\Quality\Models\QualityReview;
use JeffersonGoncalves\Erp\Quality\Models\QualityReviewObjective;

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
        'quality_goal' => QualityGoal::class,
        'quality_goal_objective' => QualityGoalObjective::class,
        'quality_procedure' => QualityProcedure::class,
        'quality_procedure_process' => QualityProcedureProcess::class,
        'quality_inspection_template' => QualityInspectionTemplate::class,
        'quality_inspection_template_parameter' => QualityInspectionTemplateParameter::class,
        'quality_inspection' => QualityInspection::class,
        'quality_inspection_reading' => QualityInspectionReading::class,
        'non_conformance' => NonConformance::class,
        'quality_action' => QualityAction::class,
        'quality_action_resolution' => QualityActionResolution::class,
        'quality_review' => QualityReview::class,
        'quality_review_objective' => QualityReviewObjective::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    |
    | Optional default quality settings. `default_inspection_type` is applied
    | to new quality inspections and `default_goal_frequency` is the review
    | cadence applied to new quality goals.
    |
    */
    'default_inspection_type' => InspectionType::Incoming->value,

    'default_goal_frequency' => 'None',
];
