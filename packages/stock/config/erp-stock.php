<?php

use JeffersonGoncalves\Erp\Stock\Models\Batch;
use JeffersonGoncalves\Erp\Stock\Models\Bin;
use JeffersonGoncalves\Erp\Stock\Models\DeliveryNote;
use JeffersonGoncalves\Erp\Stock\Models\DeliveryNoteItem;
use JeffersonGoncalves\Erp\Stock\Models\Item;
use JeffersonGoncalves\Erp\Stock\Models\ItemPrice;
use JeffersonGoncalves\Erp\Stock\Models\MaterialRequest;
use JeffersonGoncalves\Erp\Stock\Models\MaterialRequestItem;
use JeffersonGoncalves\Erp\Stock\Models\PriceList;
use JeffersonGoncalves\Erp\Stock\Models\PurchaseReceipt;
use JeffersonGoncalves\Erp\Stock\Models\PurchaseReceiptItem;
use JeffersonGoncalves\Erp\Stock\Models\SerialNo;
use JeffersonGoncalves\Erp\Stock\Models\StockEntry;
use JeffersonGoncalves\Erp\Stock\Models\StockEntryDetail;
use JeffersonGoncalves\Erp\Stock\Models\StockLedgerEntry;
use JeffersonGoncalves\Erp\Stock\Models\StockReconciliation;
use JeffersonGoncalves\Erp\Stock\Models\StockReconciliationItem;
use JeffersonGoncalves\Erp\Stock\Models\Warehouse;

return [
    /*
    |--------------------------------------------------------------------------
    | Table Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix applied to all tables created by the package. This is shared with
    | laravel-erp-core and laravel-erp-accounting so that foreign keys across
    | the ERP ecosystem resolve against a single set of prefixed tables. Set to
    | null to disable.
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
        'item' => Item::class,
        'price_list' => PriceList::class,
        'item_price' => ItemPrice::class,
        'warehouse' => Warehouse::class,
        'batch' => Batch::class,
        'serial_no' => SerialNo::class,
        'stock_ledger_entry' => StockLedgerEntry::class,
        'bin' => Bin::class,
        'stock_entry' => StockEntry::class,
        'stock_entry_detail' => StockEntryDetail::class,
        'material_request' => MaterialRequest::class,
        'material_request_item' => MaterialRequestItem::class,
        'delivery_note' => DeliveryNote::class,
        'delivery_note_item' => DeliveryNoteItem::class,
        'purchase_receipt' => PurchaseReceipt::class,
        'purchase_receipt_item' => PurchaseReceiptItem::class,
        'stock_reconciliation' => StockReconciliation::class,
        'stock_reconciliation_item' => StockReconciliationItem::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | Default valuation method applied to items that do not declare their own,
    | and whether the perpetual inventory engine permits stock to fall below
    | zero. When negative stock is not allowed an outbound movement that would
    | overdraw a bin throws a DomainException.
    |
    */
    'default_valuation_method' => 'Moving Average',

    'allow_negative_stock' => false,
];
