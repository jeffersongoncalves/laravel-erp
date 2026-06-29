<?php

namespace JeffersonGoncalves\Erp\Manufacturing;

use JeffersonGoncalves\Erp\Manufacturing\Services\WorkOrderService;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ErpManufacturingServiceProvider extends PackageServiceProvider
{
    public static string $name = 'erp-manufacturing';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasMigrations([
                'create_erp_workstations_table',
                'create_erp_operations_table',
                'create_erp_boms_table',
                'create_erp_bom_items_table',
                'create_erp_bom_operations_table',
                'create_erp_routings_table',
                'create_erp_routing_operations_table',
                'create_erp_work_orders_table',
                'create_erp_work_order_items_table',
                'create_erp_job_cards_table',
                'create_erp_job_card_time_logs_table',
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(WorkOrderService::class);
    }
}
