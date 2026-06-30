<?php

namespace JeffersonGoncalves\Erp\Stock\Support;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use JeffersonGoncalves\Erp\Stock\Models\Contracts\BinContract;
use JeffersonGoncalves\Erp\Stock\Models\Contracts\ItemContract;
use JeffersonGoncalves\Erp\Stock\Models\Contracts\StockLedgerEntryContract;
use JeffersonGoncalves\Erp\Stock\Models\Contracts\WarehouseContract;

class ModelResolver
{
    /** @var array<string, string> */
    protected static array $cache = [];

    /** @return class-string<Model&ItemContract> */
    public static function item(): string
    {
        return static::resolve('item', ItemContract::class);
    }

    /** @return class-string<Model&WarehouseContract> */
    public static function warehouse(): string
    {
        return static::resolve('warehouse', WarehouseContract::class);
    }

    /** @return class-string<Model&StockLedgerEntryContract> */
    public static function stockLedgerEntry(): string
    {
        return static::resolve('stock_ledger_entry', StockLedgerEntryContract::class);
    }

    /** @return class-string<Model&BinContract> */
    public static function bin(): string
    {
        return static::resolve('bin', BinContract::class);
    }

    /** @return class-string<Model> */
    public static function priceList(): string
    {
        return static::resolve('price_list');
    }

    /** @return class-string<Model> */
    public static function itemPrice(): string
    {
        return static::resolve('item_price');
    }

    /** @return class-string<Model> */
    public static function batch(): string
    {
        return static::resolve('batch');
    }

    /** @return class-string<Model> */
    public static function serialNo(): string
    {
        return static::resolve('serial_no');
    }

    /** @return class-string<Model> */
    public static function stockEntry(): string
    {
        return static::resolve('stock_entry');
    }

    /** @return class-string<Model> */
    public static function stockEntryDetail(): string
    {
        return static::resolve('stock_entry_detail');
    }

    /** @return class-string<Model> */
    public static function materialRequest(): string
    {
        return static::resolve('material_request');
    }

    /** @return class-string<Model> */
    public static function materialRequestItem(): string
    {
        return static::resolve('material_request_item');
    }

    /** @return class-string<Model> */
    public static function deliveryNote(): string
    {
        return static::resolve('delivery_note');
    }

    /** @return class-string<Model> */
    public static function deliveryNoteItem(): string
    {
        return static::resolve('delivery_note_item');
    }

    /** @return class-string<Model> */
    public static function purchaseReceipt(): string
    {
        return static::resolve('purchase_receipt');
    }

    /** @return class-string<Model> */
    public static function purchaseReceiptItem(): string
    {
        return static::resolve('purchase_receipt_item');
    }

    /** @return class-string<Model> */
    public static function stockReconciliation(): string
    {
        return static::resolve('stock_reconciliation');
    }

    /** @return class-string<Model> */
    public static function stockReconciliationItem(): string
    {
        return static::resolve('stock_reconciliation_item');
    }

    /** @return class-string<Model> */
    public static function putawayRule(): string
    {
        return static::resolve('putaway_rule');
    }

    /** @return class-string<Model> */
    public static function shipment(): string
    {
        return static::resolve('shipment');
    }

    /** @return class-string<Model> */
    public static function shipmentParcel(): string
    {
        return static::resolve('shipment_parcel');
    }

    /** @return class-string<Model> */
    public static function shipmentDeliveryNote(): string
    {
        return static::resolve('shipment_delivery_note');
    }

    /** @return class-string<Model> */
    public static function packingSlip(): string
    {
        return static::resolve('packing_slip');
    }

    /** @return class-string<Model> */
    public static function packingSlipItem(): string
    {
        return static::resolve('packing_slip_item');
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
        $model = config("erp-stock.models.{$key}");

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
