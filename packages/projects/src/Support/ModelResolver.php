<?php

namespace JeffersonGoncalves\Erp\Projects\Support;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use JeffersonGoncalves\Erp\Projects\Models\Contracts\ProjectContract;
use JeffersonGoncalves\Erp\Projects\Models\Contracts\TaskContract;

class ModelResolver
{
    /** @var array<string, string> */
    protected static array $cache = [];

    /** @return class-string<Model> */
    public static function activityType(): string
    {
        return static::resolve('activity_type');
    }

    /** @return class-string<Model&ProjectContract> */
    public static function project(): string
    {
        return static::resolve('project', ProjectContract::class);
    }

    /** @return class-string<Model&TaskContract> */
    public static function task(): string
    {
        return static::resolve('task', TaskContract::class);
    }

    /** @return class-string<Model> */
    public static function timesheet(): string
    {
        return static::resolve('timesheet');
    }

    /** @return class-string<Model> */
    public static function timesheetDetail(): string
    {
        return static::resolve('timesheet_detail');
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
        $model = config("erp-projects.models.{$key}");

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
