<?php

namespace JeffersonGoncalves\Erp\Crm\Support;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class ModelResolver
{
    /** @var array<string, string> */
    protected static array $cache = [];

    /** @return class-string<Model> */
    public static function leadSource(): string
    {
        return static::resolve('lead_source');
    }

    /** @return class-string<Model> */
    public static function campaign(): string
    {
        return static::resolve('campaign');
    }

    /** @return class-string<Model> */
    public static function contract(): string
    {
        return static::resolve('contract');
    }

    /** @return class-string<Model> */
    public static function lead(): string
    {
        return static::resolve('lead');
    }

    /** @return class-string<Model> */
    public static function opportunity(): string
    {
        return static::resolve('opportunity');
    }

    /** @return class-string<Model> */
    public static function opportunityItem(): string
    {
        return static::resolve('opportunity_item');
    }

    /** @return class-string<Model> */
    public static function appointment(): string
    {
        return static::resolve('appointment');
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
        $model = config("erp-crm.models.{$key}");

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
