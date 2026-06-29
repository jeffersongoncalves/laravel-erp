<?php

namespace JeffersonGoncalves\Erp\Support\Support;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class ModelResolver
{
    /** @var array<string, string> */
    protected static array $cache = [];

    /** @return class-string<Model> */
    public static function issueType(): string
    {
        return static::resolve('issue_type');
    }

    /** @return class-string<Model> */
    public static function serviceLevelAgreement(): string
    {
        return static::resolve('service_level_agreement');
    }

    /** @return class-string<Model> */
    public static function serviceLevelPriority(): string
    {
        return static::resolve('service_level_priority');
    }

    /** @return class-string<Model> */
    public static function issue(): string
    {
        return static::resolve('issue');
    }

    /** @return class-string<Model> */
    public static function warrantyClaim(): string
    {
        return static::resolve('warranty_claim');
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
        $model = config("erp-support.models.{$key}");

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
