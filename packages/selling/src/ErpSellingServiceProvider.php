<?php

namespace JeffersonGoncalves\Erp\Selling;

use JeffersonGoncalves\Erp\Selling\Models\Contracts\CustomerContract;
use JeffersonGoncalves\Erp\Selling\Models\Contracts\CustomerGroupContract;
use JeffersonGoncalves\Erp\Selling\Services\QuotationService;
use JeffersonGoncalves\Erp\Selling\Services\SalesOrderService;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ErpSellingServiceProvider extends PackageServiceProvider
{
    public static string $name = 'erp-selling';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasMigrations([
                'create_erp_customer_groups_table',
                'create_erp_customers_table',
                'create_erp_sales_partners_table',
                'create_erp_product_bundles_table',
                'create_erp_product_bundle_items_table',
                'create_erp_quotations_table',
                'create_erp_quotation_items_table',
                'create_erp_sales_orders_table',
                'create_erp_sales_order_items_table',
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(SalesOrderService::class);
        $this->app->singleton(QuotationService::class);
    }

    public function packageBooted(): void
    {
        $this->registerModelBindings();
    }

    protected function registerModelBindings(): void
    {
        $bindings = [
            CustomerGroupContract::class => 'customer_group',
            CustomerContract::class => 'customer',
        ];

        foreach ($bindings as $contract => $configKey) {
            $this->app->bind($contract, config("erp-selling.models.{$configKey}"));
        }
    }
}
