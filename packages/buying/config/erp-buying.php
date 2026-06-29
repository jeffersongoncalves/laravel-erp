<?php

use JeffersonGoncalves\Erp\Buying\Models\BuyingSetting;
use JeffersonGoncalves\Erp\Buying\Models\PurchaseOrder;
use JeffersonGoncalves\Erp\Buying\Models\PurchaseOrderItem;
use JeffersonGoncalves\Erp\Buying\Models\RequestForQuotation;
use JeffersonGoncalves\Erp\Buying\Models\RequestForQuotationItem;
use JeffersonGoncalves\Erp\Buying\Models\RequestForQuotationSupplier;
use JeffersonGoncalves\Erp\Buying\Models\Supplier;
use JeffersonGoncalves\Erp\Buying\Models\SupplierGroup;
use JeffersonGoncalves\Erp\Buying\Models\SupplierQuotation;
use JeffersonGoncalves\Erp\Buying\Models\SupplierQuotationItem;

return [
    /*
    |--------------------------------------------------------------------------
    | Table Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix applied to all tables created by the package. This is shared with
    | laravel-erp-core, laravel-erp-accounting and laravel-erp-stock so that
    | foreign keys across the ERP ecosystem resolve against a single set of
    | prefixed tables. Set to null to disable.
    |
    */
    'table_prefix' => 'erp_',

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | Models used by the package. Can be overridden to extend the default
    | behavior. Swappable models that ship a contract must implement it
    | (see src/Models/Contracts/).
    |
    */
    'models' => [
        'supplier_group' => SupplierGroup::class,
        'supplier' => Supplier::class,
        'buying_setting' => BuyingSetting::class,
        'request_for_quotation' => RequestForQuotation::class,
        'request_for_quotation_item' => RequestForQuotationItem::class,
        'request_for_quotation_supplier' => RequestForQuotationSupplier::class,
        'supplier_quotation' => SupplierQuotation::class,
        'supplier_quotation_item' => SupplierQuotationItem::class,
        'purchase_order' => PurchaseOrder::class,
        'purchase_order_item' => PurchaseOrderItem::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | Default supplier group applied to new suppliers and the default buying
    | price list used to fetch purchase rates. Both are optional.
    |
    */
    'default_supplier_group' => null,

    'default_buying_price_list' => null,
];
