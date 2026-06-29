<?php

namespace JeffersonGoncalves\Erp\Selling\Support;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use JeffersonGoncalves\Erp\Selling\Models\Contracts\CustomerContract;
use JeffersonGoncalves\Erp\Selling\Models\Contracts\CustomerGroupContract;

class ModelResolver
{
    /** @var array<string, string> */
    protected static array $cache = [];

    /** @return class-string<Model&CustomerGroupContract> */
    public static function customerGroup(): string
    {
        return static::resolve('customer_group', CustomerGroupContract::class);
    }

    /** @return class-string<Model&CustomerContract> */
    public static function customer(): string
    {
        return static::resolve('customer', CustomerContract::class);
    }

    /** @return class-string<Model> */
    public static function salesPartner(): string
    {
        return static::resolve('sales_partner');
    }

    /** @return class-string<Model> */
    public static function productBundle(): string
    {
        return static::resolve('product_bundle');
    }

    /** @return class-string<Model> */
    public static function productBundleItem(): string
    {
        return static::resolve('product_bundle_item');
    }

    /** @return class-string<Model> */
    public static function quotation(): string
    {
        return static::resolve('quotation');
    }

    /** @return class-string<Model> */
    public static function quotationItem(): string
    {
        return static::resolve('quotation_item');
    }

    /** @return class-string<Model> */
    public static function salesOrder(): string
    {
        return static::resolve('sales_order');
    }

    /** @return class-string<Model> */
    public static function salesOrderItem(): string
    {
        return static::resolve('sales_order_item');
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
        $model = config("erp-selling.models.{$key}");

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
