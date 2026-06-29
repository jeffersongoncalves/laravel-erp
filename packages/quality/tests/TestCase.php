<?php

namespace JeffersonGoncalves\Erp\Quality\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use JeffersonGoncalves\Erp\Core\ErpCoreServiceProvider;
use JeffersonGoncalves\Erp\Quality\ErpQualityServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(function (string $modelName): string {
            $basename = class_basename($modelName);

            foreach (['Quality', 'Core'] as $package) {
                $factory = "JeffersonGoncalves\\Erp\\{$package}\\Database\\Factories\\{$basename}Factory";

                if (class_exists($factory)) {
                    return $factory;
                }
            }

            return "JeffersonGoncalves\\Erp\\Quality\\Database\\Factories\\{$basename}Factory";
        });
    }

    protected function getPackageProviders($app): array
    {
        return [
            ErpCoreServiceProvider::class,
            ErpQualityServiceProvider::class,
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

        $configPath = __DIR__.'/../config/erp-quality.php';
        if (file_exists($configPath)) {
            $app['config']->set('erp-quality', require $configPath);
        }
    }

    protected function defineDatabaseMigrations(): void
    {
        $tempPath = sys_get_temp_dir().'/laravel-erp-quality-migrations';

        if (is_dir($tempPath)) {
            array_map('unlink', (array) glob($tempPath.'/*.php'));
        } else {
            mkdir($tempPath, 0755, true);
        }

        $corePath = $this->erpDepPath('laravel-erp-core').'/database/migrations';
        $packagePath = __DIR__.'/../database/migrations';

        // Foreign-key-safe order. loadMigrationsFrom sorts by filename, so each
        // stub is copied with a numeric prefix that preserves dependency order
        // across the core and quality packages (core first, then quality).
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
            'create_erp_quality_goals_table',
            'create_erp_quality_goal_objectives_table',
            'create_erp_quality_procedures_table',
            'create_erp_quality_procedure_processes_table',
            'create_erp_quality_inspection_templates_table',
            'create_erp_quality_inspection_template_parameters_table',
            'create_erp_quality_inspections_table',
            'create_erp_quality_inspection_readings_table',
            'create_erp_non_conformances_table',
            'create_erp_quality_actions_table',
            'create_erp_quality_action_resolutions_table',
            'create_erp_quality_reviews_table',
            'create_erp_quality_review_objectives_table',
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
