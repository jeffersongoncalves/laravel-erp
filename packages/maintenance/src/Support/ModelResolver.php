<?php

namespace JeffersonGoncalves\Erp\Maintenance\Support;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class ModelResolver
{
    /** @var array<string, string> */
    protected static array $cache = [];

    /** @return class-string<Model> */
    public static function maintenanceSchedule(): string
    {
        return static::resolve('maintenance_schedule');
    }

    /** @return class-string<Model> */
    public static function maintenanceScheduleItem(): string
    {
        return static::resolve('maintenance_schedule_item');
    }

    /** @return class-string<Model> */
    public static function maintenanceScheduleDetail(): string
    {
        return static::resolve('maintenance_schedule_detail');
    }

    /** @return class-string<Model> */
    public static function maintenanceVisit(): string
    {
        return static::resolve('maintenance_visit');
    }

    /** @return class-string<Model> */
    public static function maintenanceVisitPurpose(): string
    {
        return static::resolve('maintenance_visit_purpose');
    }

    /**
     * @return class-string
     *
     * @throws InvalidArgumentException
     */
    protected static function resolve(string $key): string
    {
        if (isset(static::$cache[$key])) {
            return static::$cache[$key];
        }

        /** @var class-string|null $model */
        $model = config("erp-maintenance.models.{$key}");

        if (! $model || ! class_exists($model)) {
            throw new InvalidArgumentException(
                "Model class for [{$key}] does not exist: {$model}"
            );
        }

        return static::$cache[$key] = $model;
    }

    public static function flushCache(): void
    {
        static::$cache = [];
    }
}
