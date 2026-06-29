<?php

namespace JeffersonGoncalves\Erp\Manufacturing\Support;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class ModelResolver
{
    /** @var array<string, string> */
    protected static array $cache = [];

    /** @return class-string<Model> */
    public static function workstation(): string
    {
        return static::resolve('workstation');
    }

    /** @return class-string<Model> */
    public static function operation(): string
    {
        return static::resolve('operation');
    }

    /** @return class-string<Model> */
    public static function bom(): string
    {
        return static::resolve('bom');
    }

    /** @return class-string<Model> */
    public static function bomItem(): string
    {
        return static::resolve('bom_item');
    }

    /** @return class-string<Model> */
    public static function bomOperation(): string
    {
        return static::resolve('bom_operation');
    }

    /** @return class-string<Model> */
    public static function routing(): string
    {
        return static::resolve('routing');
    }

    /** @return class-string<Model> */
    public static function routingOperation(): string
    {
        return static::resolve('routing_operation');
    }

    /** @return class-string<Model> */
    public static function workOrder(): string
    {
        return static::resolve('work_order');
    }

    /** @return class-string<Model> */
    public static function workOrderItem(): string
    {
        return static::resolve('work_order_item');
    }

    /** @return class-string<Model> */
    public static function jobCard(): string
    {
        return static::resolve('job_card');
    }

    /** @return class-string<Model> */
    public static function jobCardTimeLog(): string
    {
        return static::resolve('job_card_time_log');
    }

    /**
     * @return class-string<Model>
     *
     * @throws InvalidArgumentException
     */
    protected static function resolve(string $key): string
    {
        if (isset(static::$cache[$key])) {
            return static::$cache[$key];
        }

        /** @var class-string<Model>|null $model */
        $model = config("erp-manufacturing.models.{$key}");

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
