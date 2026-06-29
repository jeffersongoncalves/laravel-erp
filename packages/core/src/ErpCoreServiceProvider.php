<?php

namespace JeffersonGoncalves\Erp\Core;

use JeffersonGoncalves\Erp\Core\Models\Contracts\AddressContract;
use JeffersonGoncalves\Erp\Core\Models\Contracts\BrandContract;
use JeffersonGoncalves\Erp\Core\Models\Contracts\CompanyContract;
use JeffersonGoncalves\Erp\Core\Models\Contracts\ContactContract;
use JeffersonGoncalves\Erp\Core\Models\Contracts\CurrencyContract;
use JeffersonGoncalves\Erp\Core\Models\Contracts\CurrencyExchangeContract;
use JeffersonGoncalves\Erp\Core\Models\Contracts\DepartmentContract;
use JeffersonGoncalves\Erp\Core\Models\Contracts\DesignationContract;
use JeffersonGoncalves\Erp\Core\Models\Contracts\FiscalYearContract;
use JeffersonGoncalves\Erp\Core\Models\Contracts\NamingSeriesContract;
use JeffersonGoncalves\Erp\Core\Models\Contracts\TermsAndConditionsContract;
use JeffersonGoncalves\Erp\Core\Models\Contracts\UomContract;
use JeffersonGoncalves\Erp\Core\Models\Contracts\UomConversionContract;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ErpCoreServiceProvider extends PackageServiceProvider
{
    public static string $name = 'erp-core';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasMigrations([
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
            ]);
    }

    public function packageBooted(): void
    {
        $this->registerModelBindings();
    }

    protected function registerModelBindings(): void
    {
        $bindings = [
            CompanyContract::class => 'company',
            CurrencyContract::class => 'currency',
            CurrencyExchangeContract::class => 'currency_exchange',
            UomContract::class => 'uom',
            UomConversionContract::class => 'uom_conversion',
            FiscalYearContract::class => 'fiscal_year',
            DepartmentContract::class => 'department',
            DesignationContract::class => 'designation',
            BrandContract::class => 'brand',
            TermsAndConditionsContract::class => 'terms_and_conditions',
            AddressContract::class => 'address',
            ContactContract::class => 'contact',
            NamingSeriesContract::class => 'naming_series',
        ];

        foreach ($bindings as $contract => $configKey) {
            $this->app->bind($contract, config("erp-core.models.{$configKey}"));
        }
    }
}
