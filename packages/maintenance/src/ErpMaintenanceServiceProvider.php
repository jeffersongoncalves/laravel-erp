<?php

namespace JeffersonGoncalves\Erp\Maintenance;

use JeffersonGoncalves\Erp\Maintenance\Services\MaintenanceScheduleService;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ErpMaintenanceServiceProvider extends PackageServiceProvider
{
    public static string $name = 'erp-maintenance';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasMigrations([
                'create_erp_maintenance_schedules_table',
                'create_erp_maintenance_schedule_items_table',
                'create_erp_maintenance_schedule_details_table',
                'create_erp_maintenance_visits_table',
                'create_erp_maintenance_visit_purposes_table',
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(MaintenanceScheduleService::class);
    }
}
