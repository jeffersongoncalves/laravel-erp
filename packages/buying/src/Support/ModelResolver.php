<?php

namespace JeffersonGoncalves\Erp\Buying\Support;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use JeffersonGoncalves\Erp\Buying\Models\Contracts\SupplierContract;
use JeffersonGoncalves\Erp\Buying\Models\Contracts\SupplierGroupContract;

class ModelResolver
{
    /** @var array<string, string> */
    protected static array $cache = [];

    /** @return class-string<Model&SupplierGroupContract> */
    public static function supplierGroup(): string
    {
        return static::resolve('supplier_group', SupplierGroupContract::class);
    }

    /** @return class-string<Model&SupplierContract> */
    public static function supplier(): string
    {
        return static::resolve('supplier', SupplierContract::class);
    }

    /** @return class-string<Model> */
    public static function buyingSetting(): string
    {
        return static::resolve('buying_setting');
    }

    /** @return class-string<Model> */
    public static function requestForQuotation(): string
    {
        return static::resolve('request_for_quotation');
    }

    /** @return class-string<Model> */
    public static function requestForQuotationItem(): string
    {
        return static::resolve('request_for_quotation_item');
    }

    /** @return class-string<Model> */
    public static function requestForQuotationSupplier(): string
    {
        return static::resolve('request_for_quotation_supplier');
    }

    /** @return class-string<Model> */
    public static function supplierQuotation(): string
    {
        return static::resolve('supplier_quotation');
    }

    /** @return class-string<Model> */
    public static function supplierQuotationItem(): string
    {
        return static::resolve('supplier_quotation_item');
    }

    /** @return class-string<Model> */
    public static function purchaseOrder(): string
    {
        return static::resolve('purchase_order');
    }

    /** @return class-string<Model> */
    public static function purchaseOrderItem(): string
    {
        return static::resolve('purchase_order_item');
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
        $model = config("erp-buying.models.{$key}");

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
