<?php

namespace JeffersonGoncalves\Erp\Hr;

use JeffersonGoncalves\Erp\Hr\Models\Contracts\EmployeeContract;
use JeffersonGoncalves\Erp\Hr\Models\Contracts\SalaryStructureContract;
use JeffersonGoncalves\Erp\Hr\Services\LeaveService;
use JeffersonGoncalves\Erp\Hr\Services\PayrollEntryService;
use JeffersonGoncalves\Erp\Hr\Services\SalarySlipService;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ErpHrServiceProvider extends PackageServiceProvider
{
    public static string $name = 'erp-hr';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasMigrations([
                'create_erp_leave_types_table',
                'create_erp_holiday_lists_table',
                'create_erp_holidays_table',
                'create_erp_salary_components_table',
                'create_erp_employees_table',
                'create_erp_salary_structures_table',
                'create_erp_salary_structure_components_table',
                'create_erp_salary_structure_assignments_table',
                'create_erp_attendances_table',
                'create_erp_leave_applications_table',
                'create_erp_salary_slips_table',
                'create_erp_salary_slip_components_table',
                'create_erp_payroll_entries_table',
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(SalarySlipService::class);
        $this->app->singleton(PayrollEntryService::class);
        $this->app->singleton(LeaveService::class);
    }

    public function packageBooted(): void
    {
        $this->registerModelBindings();
    }

    protected function registerModelBindings(): void
    {
        $bindings = [
            EmployeeContract::class => 'employee',
            SalaryStructureContract::class => 'salary_structure',
        ];

        foreach ($bindings as $contract => $configKey) {
            $this->app->bind($contract, config("erp-hr.models.{$configKey}"));
        }
    }
}
