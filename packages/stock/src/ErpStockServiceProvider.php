<?php

namespace JeffersonGoncalves\Erp\Stock;

use JeffersonGoncalves\Erp\Accounting\Services\GeneralLedgerService;
use JeffersonGoncalves\Erp\Stock\Models\Contracts\BinContract;
use JeffersonGoncalves\Erp\Stock\Models\Contracts\ItemContract;
use JeffersonGoncalves\Erp\Stock\Models\Contracts\StockLedgerEntryContract;
use JeffersonGoncalves\Erp\Stock\Models\Contracts\WarehouseContract;
use JeffersonGoncalves\Erp\Stock\Services\StockLedgerService;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ErpStockServiceProvider extends PackageServiceProvider
{
    public static string $name = 'erp-stock';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasMigrations([
                'create_erp_warehouses_table',
                'create_erp_items_table',
                'create_erp_price_lists_table',
                'create_erp_item_prices_table',
                'create_erp_batches_table',
                'create_erp_serial_nos_table',
                'create_erp_stock_ledger_entries_table',
                'create_erp_bins_table',
                'create_erp_stock_entries_table',
                'create_erp_stock_entry_details_table',
                'create_erp_material_requests_table',
                'create_erp_material_request_items_table',
                'create_erp_delivery_notes_table',
                'create_erp_delivery_note_items_table',
                'create_erp_purchase_receipts_table',
                'create_erp_purchase_receipt_items_table',
                'create_erp_stock_reconciliations_table',
                'create_erp_stock_reconciliation_items_table',
                'create_erp_putaway_rules_table',
                'create_erp_shipments_table',
                'create_erp_shipment_parcels_table',
                'create_erp_shipment_delivery_notes_table',
                'create_erp_packing_slips_table',
                'create_erp_packing_slip_items_table',
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(StockLedgerService::class, function ($app): StockLedgerService {
            return new StockLedgerService($app->make(GeneralLedgerService::class));
        });
    }

    public function packageBooted(): void
    {
        $this->registerModelBindings();
    }

    protected function registerModelBindings(): void
    {
        $bindings = [
            ItemContract::class => 'item',
            WarehouseContract::class => 'warehouse',
            StockLedgerEntryContract::class => 'stock_ledger_entry',
            BinContract::class => 'bin',
        ];

        foreach ($bindings as $contract => $configKey) {
            $this->app->bind($contract, config("erp-stock.models.{$configKey}"));
        }
    }
}
