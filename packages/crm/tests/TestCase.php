<?php

namespace JeffersonGoncalves\Erp\Crm\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use JeffersonGoncalves\Erp\Accounting\ErpAccountingServiceProvider;
use JeffersonGoncalves\Erp\Core\ErpCoreServiceProvider;
use JeffersonGoncalves\Erp\Crm\ErpCrmServiceProvider;
use JeffersonGoncalves\Erp\Selling\ErpSellingServiceProvider;
use JeffersonGoncalves\Erp\Stock\ErpStockServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(function (string $modelName): string {
            $basename = class_basename($modelName);

            foreach (['Crm', 'Selling', 'Stock', 'Accounting', 'Core'] as $package) {
                $factory = "JeffersonGoncalves\\Erp\\{$package}\\Database\\Factories\\{$basename}Factory";

                if (class_exists($factory)) {
                    return $factory;
                }
            }

            return "JeffersonGoncalves\\Erp\\Crm\\Database\\Factories\\{$basename}Factory";
        });
    }

    protected function getPackageProviders($app): array
    {
        return [
            ErpCoreServiceProvider::class,
            ErpAccountingServiceProvider::class,
            ErpStockServiceProvider::class,
            ErpSellingServiceProvider::class,
            ErpCrmServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);

        $coreConfig = $this->erpDepPath('laravel-erp-core').'/config/erp-core.php';
        if (file_exists($coreConfig)) {
            $app['config']->set('erp-core', require $coreConfig);
        }

        $accountingConfig = $this->erpDepPath('laravel-erp-accounting').'/config/erp-accounting.php';
        if (file_exists($accountingConfig)) {
            $app['config']->set('erp-accounting', require $accountingConfig);
        }

        $stockConfig = $this->erpDepPath('laravel-erp-stock').'/config/erp-stock.php';
        if (file_exists($stockConfig)) {
            $app['config']->set('erp-stock', require $stockConfig);
        }

        $sellingConfig = $this->erpDepPath('laravel-erp-selling').'/config/erp-selling.php';
        if (file_exists($sellingConfig)) {
            $app['config']->set('erp-selling', require $sellingConfig);
        }

        $configPath = __DIR__.'/../config/erp-crm.php';
        if (file_exists($configPath)) {
            $app['config']->set('erp-crm', require $configPath);
        }
    }

    protected function defineDatabaseMigrations(): void
    {
        $tempPath = sys_get_temp_dir().'/laravel-erp-crm-migrations';

        if (is_dir($tempPath)) {
            array_map('unlink', (array) glob($tempPath.'/*.php'));
        } else {
            mkdir($tempPath, 0755, true);
        }

        $corePath = $this->erpDepPath('laravel-erp-core').'/database/migrations';
        $accountingPath = $this->erpDepPath('laravel-erp-accounting').'/database/migrations';
        $stockPath = $this->erpDepPath('laravel-erp-stock').'/database/migrations';
        $sellingPath = $this->erpDepPath('laravel-erp-selling').'/database/migrations';
        $packagePath = __DIR__.'/../database/migrations';

        // Foreign-key-safe order. loadMigrationsFrom sorts by filename, so each
        // stub is copied with a numeric prefix that preserves dependency order
        // across the core, accounting, stock, selling and crm packages (core
        // first, then accounting, then stock, then selling, then crm).
        $ordered = array_merge(
            array_map(fn (string $name) => [$corePath, $name], $this->coreMigrations()),
            array_map(fn (string $name) => [$accountingPath, $name], $this->accountingMigrations()),
            array_map(fn (string $name) => [$stockPath, $name], $this->stockMigrations()),
            array_map(fn (string $name) => [$sellingPath, $name], $this->sellingMigrations()),
            array_map(fn (string $name) => [$packagePath, $name], $this->packageMigrations()),
        );

        foreach ($ordered as $index => [$path, $name]) {
            $stub = $path.'/'.$name.'.php.stub';

            if (file_exists($stub)) {
                copy($stub, sprintf('%s/%04d_%s.php', $tempPath, $index, $name));
            }
        }

        $this->loadMigrationsFrom($tempPath);
    }

    /** @return list<string> */
    protected function coreMigrations(): array
    {
        return [
            'create_erp_companies_table',
            'create_erp_currencies_table',
            'create_erp_currency_exchanges_table',
            'create_erp_uoms_table',
            'create_erp_uom_conversions_table',
            'create_erp_fiscal_years_table',
            'create_erp_departments_table',
            'create_erp_designations_table',
            'create_erp_brands_table',
            'create_erp_terms_and_conditions_table',
            'create_erp_addresses_table',
            'create_erp_contacts_table',
            'create_erp_naming_series_table',
        ];
    }

    /** @return list<string> */
    protected function accountingMigrations(): array
    {
        return [
            'create_erp_accounts_table',
            'create_erp_cost_centers_table',
            'create_erp_payment_terms_table',
            'create_erp_modes_of_payment_table',
            'create_erp_tax_templates_table',
            'create_erp_tax_template_taxes_table',
            'create_erp_banks_table',
            'create_erp_bank_accounts_table',
            'create_erp_budgets_table',
            'create_erp_budget_accounts_table',
            'create_erp_gl_entries_table',
            'create_erp_journal_entries_table',
            'create_erp_journal_entry_accounts_table',
            'create_erp_payment_entries_table',
            'create_erp_sales_invoices_table',
            'create_erp_sales_invoice_items_table',
            'create_erp_sales_invoice_taxes_table',
            'create_erp_purchase_invoices_table',
            'create_erp_purchase_invoice_items_table',
            'create_erp_purchase_invoice_taxes_table',
            'create_erp_period_closing_vouchers_table',
            'create_erp_bank_transactions_table',
        ];
    }

    /** @return list<string> */
    protected function stockMigrations(): array
    {
        return [
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
        ];
    }

    /** @return list<string> */
    protected function sellingMigrations(): array
    {
        return [
            'create_erp_customer_groups_table',
            'create_erp_customers_table',
            'create_erp_sales_partners_table',
            'create_erp_product_bundles_table',
            'create_erp_product_bundle_items_table',
            'create_erp_quotations_table',
            'create_erp_quotation_items_table',
            'create_erp_sales_orders_table',
            'create_erp_sales_order_items_table',
        ];
    }

    /** @return list<string> */
    protected function packageMigrations(): array
    {
        return [
            'create_erp_lead_sources_table',
            'create_erp_campaigns_table',
            'create_erp_contracts_table',
            'create_erp_leads_table',
            'create_erp_opportunities_table',
            'create_erp_opportunity_items_table',
            'create_erp_appointments_table',
        ];
    }

    /**
     * Resolve a sibling ERP package directory.
     *
     * Works both standalone (dependency installed under vendor/) and inside the
     * monorepo (sibling under packages/, where directories drop the laravel-erp- prefix).
     */
    private function erpDepPath(string $package): string
    {
        $vendor = __DIR__.'/../vendor/jeffersongoncalves/'.$package;

        if (is_dir($vendor)) {
            return $vendor;
        }

        return __DIR__.'/../../'.str_replace('laravel-erp-', '', $package);
    }
}
