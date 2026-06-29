<?php

namespace JeffersonGoncalves\Erp\Crm;

use JeffersonGoncalves\Erp\Crm\Services\LeadService;
use JeffersonGoncalves\Erp\Crm\Services\OpportunityService;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ErpCrmServiceProvider extends PackageServiceProvider
{
    public static string $name = 'erp-crm';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasMigrations([
                'create_erp_lead_sources_table',
                'create_erp_campaigns_table',
                'create_erp_contracts_table',
                'create_erp_leads_table',
                'create_erp_opportunities_table',
                'create_erp_opportunity_items_table',
                'create_erp_appointments_table',
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(LeadService::class);
        $this->app->singleton(OpportunityService::class);
    }
}
