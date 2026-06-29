<?php

namespace JeffersonGoncalves\Erp\Projects;

use JeffersonGoncalves\Erp\Projects\Models\Contracts\ProjectContract;
use JeffersonGoncalves\Erp\Projects\Models\Contracts\TaskContract;
use JeffersonGoncalves\Erp\Projects\Services\TimesheetService;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ErpProjectsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'erp-projects';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasMigrations([
                'create_erp_activity_types_table',
                'create_erp_projects_table',
                'create_erp_tasks_table',
                'create_erp_timesheets_table',
                'create_erp_timesheet_details_table',
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(TimesheetService::class);
    }

    public function packageBooted(): void
    {
        $this->registerModelBindings();
    }

    protected function registerModelBindings(): void
    {
        $bindings = [
            ProjectContract::class => 'project',
            TaskContract::class => 'task',
        ];

        foreach ($bindings as $contract => $configKey) {
            $this->app->bind($contract, config("erp-projects.models.{$configKey}"));
        }
    }
}
