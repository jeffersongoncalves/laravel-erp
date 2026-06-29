<?php

namespace JeffersonGoncalves\Erp\Quality;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ErpQualityServiceProvider extends PackageServiceProvider
{
    public static string $name = 'erp-quality';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasMigrations([
                'create_erp_quality_goals_table',
                'create_erp_quality_goal_objectives_table',
                'create_erp_quality_procedures_table',
                'create_erp_quality_procedure_processes_table',
                'create_erp_quality_inspection_templates_table',
                'create_erp_quality_inspection_template_parameters_table',
                'create_erp_quality_inspections_table',
                'create_erp_quality_inspection_readings_table',
                'create_erp_non_conformances_table',
                'create_erp_quality_actions_table',
                'create_erp_quality_action_resolutions_table',
                'create_erp_quality_reviews_table',
                'create_erp_quality_review_objectives_table',
            ]);
    }
}
