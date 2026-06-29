<?php

namespace JeffersonGoncalves\Erp\Support\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use JeffersonGoncalves\Erp\Core\ErpCoreServiceProvider;
use JeffersonGoncalves\Erp\Support\ErpSupportServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(function (string $modelName): string {
            $basename = class_basename($modelName);

            foreach (['Support', 'Core'] as $package) {
                $factory = "JeffersonGoncalves\\Erp\\{$package}\\Database\\Factories\\{$basename}Factory";

                if (class_exists($factory)) {
                    return $factory;
                }
            }

            return "JeffersonGoncalves\\Erp\\Support\\Database\\Factories\\{$basename}Factory";
        });
    }

    protected function getPackageProviders($app): array
    {
        return [
            ErpCoreServiceProvider::class,
            ErpSupportServiceProvider::class,
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

        $configPath = __DIR__.'/../config/erp-support.php';
        if (file_exists($configPath)) {
            $app['config']->set('erp-support', require $configPath);
        }
    }

    protected function defineDatabaseMigrations(): void
    {
        $tempPath = sys_get_temp_dir().'/laravel-erp-support-migrations';

        if (is_dir($tempPath)) {
            array_map('unlink', (array) glob($tempPath.'/*.php'));
        } else {
            mkdir($tempPath, 0755, true);
        }

        $corePath = $this->erpDepPath('laravel-erp-core').'/database/migrations';
        $packagePath = __DIR__.'/../database/migrations';

        // Foreign-key-safe order. loadMigrationsFrom sorts by filename, so each
        // stub is copied with a numeric prefix that preserves dependency order
        // across the core and support packages (core first, then support).
        $ordered = array_merge(
            array_map(fn (string $name) => [$corePath, $name], $this->coreMigrations()),
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
    protected function packageMigrations(): array
    {
        return [
            'create_erp_issue_types_table',
            'create_erp_service_level_agreements_table',
            'create_erp_service_level_priorities_table',
            'create_erp_issues_table',
            'create_erp_warranty_claims_table',
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
