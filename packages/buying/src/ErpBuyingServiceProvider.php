<?php

namespace JeffersonGoncalves\Erp\Buying;

use JeffersonGoncalves\Erp\Buying\Models\Contracts\SupplierContract;
use JeffersonGoncalves\Erp\Buying\Models\Contracts\SupplierGroupContract;
use JeffersonGoncalves\Erp\Buying\Services\PurchaseOrderService;
use JeffersonGoncalves\Erp\Buying\Services\SupplierQuotationService;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ErpBuyingServiceProvider extends PackageServiceProvider
{
    public static string $name = 'erp-buying';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasMigrations([
                'create_erp_supplier_groups_table',
                'create_erp_suppliers_table',
                'create_erp_buying_settings_table',
                'create_erp_request_for_quotations_table',
                'create_erp_request_for_quotation_items_table',
                'create_erp_request_for_quotation_suppliers_table',
                'create_erp_supplier_quotations_table',
                'create_erp_supplier_quotation_items_table',
                'create_erp_purchase_orders_table',
                'create_erp_purchase_order_items_table',
                'create_erp_blanket_orders_table',
                'create_erp_blanket_order_items_table',
                'create_erp_supplier_scorecards_table',
                'create_erp_supplier_scorecard_criteria_table',
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(PurchaseOrderService::class);
        $this->app->singleton(SupplierQuotationService::class);
    }

    public function packageBooted(): void
    {
        $this->registerModelBindings();
    }

    protected function registerModelBindings(): void
    {
        $bindings = [
            SupplierGroupContract::class => 'supplier_group',
            SupplierContract::class => 'supplier',
        ];

        foreach ($bindings as $contract => $configKey) {
            $this->app->bind($contract, config("erp-buying.models.{$configKey}"));
        }
    }
}
