<?php

namespace JeffersonGoncalves\Erp\Support;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ErpSupportServiceProvider extends PackageServiceProvider
{
    public static string $name = 'erp-support';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasMigrations([
                'create_erp_issue_types_table',
                'create_erp_service_level_agreements_table',
                'create_erp_service_level_priorities_table',
                'create_erp_issues_table',
                'create_erp_warranty_claims_table',
            ]);
    }
}
