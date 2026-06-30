<?php

namespace JeffersonGoncalves\Erp\Hr\Support;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use JeffersonGoncalves\Erp\Hr\Models\Contracts\EmployeeContract;
use JeffersonGoncalves\Erp\Hr\Models\Contracts\SalaryStructureContract;

class ModelResolver
{
    /** @var array<string, string> */
    protected static array $cache = [];

    /** @return class-string<Model&EmployeeContract> */
    public static function employee(): string
    {
        return static::resolve('employee', EmployeeContract::class);
    }

    /** @return class-string<Model> */
    public static function leaveType(): string
    {
        return static::resolve('leave_type');
    }

    /** @return class-string<Model> */
    public static function holidayList(): string
    {
        return static::resolve('holiday_list');
    }

    /** @return class-string<Model> */
    public static function holiday(): string
    {
        return static::resolve('holiday');
    }

    /** @return class-string<Model> */
    public static function salaryComponent(): string
    {
        return static::resolve('salary_component');
    }

    /** @return class-string<Model&SalaryStructureContract> */
    public static function salaryStructure(): string
    {
        return static::resolve('salary_structure', SalaryStructureContract::class);
    }

    /** @return class-string<Model> */
    public static function salaryStructureComponent(): string
    {
        return static::resolve('salary_structure_component');
    }

    /** @return class-string<Model> */
    public static function salaryStructureAssignment(): string
    {
        return static::resolve('salary_structure_assignment');
    }

    /** @return class-string<Model> */
    public static function attendance(): string
    {
        return static::resolve('attendance');
    }

    /** @return class-string<Model> */
    public static function leaveApplication(): string
    {
        return static::resolve('leave_application');
    }

    /** @return class-string<Model> */
    public static function salarySlip(): string
    {
        return static::resolve('salary_slip');
    }

    /** @return class-string<Model> */
    public static function salarySlipComponent(): string
    {
        return static::resolve('salary_slip_component');
    }

    /** @return class-string<Model> */
    public static function payrollEntry(): string
    {
        return static::resolve('payroll_entry');
    }

    /**
     * @param  class-string|null  $contract
     * @return class-string
     *
     * @throws InvalidArgumentException
     */
    protected static function resolve(string $key, ?string $contract = null): string
    {
        if (isset(static::$cache[$key])) {
            return static::$cache[$key];
        }

        /** @var class-string|null $model */
        $model = config("erp-hr.models.{$key}");

        if (! $model || ! class_exists($model)) {
            throw new InvalidArgumentException(
                "Model class for [{$key}] does not exist: {$model}"
            );
        }

        if ($contract !== null && ! is_a($model, $contract, true)) {
            throw new InvalidArgumentException(
                "Model [{$model}] must implement [{$contract}]."
            );
        }

        return static::$cache[$key] = $model;
    }

    public static function flushCache(): void
    {
        static::$cache = [];
    }
}
