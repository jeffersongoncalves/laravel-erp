<?php

use JeffersonGoncalves\Erp\Selling\Models\Customer;
use JeffersonGoncalves\Erp\Selling\Models\CustomerGroup;
use JeffersonGoncalves\Erp\Selling\Models\ProductBundle;
use JeffersonGoncalves\Erp\Selling\Models\ProductBundleItem;
use JeffersonGoncalves\Erp\Selling\Models\Quotation;
use JeffersonGoncalves\Erp\Selling\Models\QuotationItem;
use JeffersonGoncalves\Erp\Selling\Models\SalesOrder;
use JeffersonGoncalves\Erp\Selling\Models\SalesOrderItem;
use JeffersonGoncalves\Erp\Selling\Models\SalesPartner;

return [
    /*
    |--------------------------------------------------------------------------
    | Table Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix applied to all tables created by the package to avoid
    | collision with existing application tables.
    | Set to null to use table names without a prefix.
    |
    */
    'table_prefix' => 'erp_',

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | Models used by the package. Can be overridden to extend the default
    | behavior. Custom models must implement the corresponding contract
    | interface (see src/Models/Contracts/).
    |
    */
    'models' => [
        'customer_group' => CustomerGroup::class,
        'customer' => Customer::class,
        'sales_partner' => SalesPartner::class,
        'product_bundle' => ProductBundle::class,
        'product_bundle_item' => ProductBundleItem::class,
        'quotation' => Quotation::class,
        'quotation_item' => QuotationItem::class,
        'sales_order' => SalesOrder::class,
        'sales_order_item' => SalesOrderItem::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    |
    | Optional default selling settings. `default_price_list` references a
    | stock price list and `default_customer_group` a customer group, applied
    | when a customer leaves them blank.
    |
    */
    'default_price_list' => null,

    'default_customer_group' => null,
];
