<div class="filament-hidden">

![Laravel ERP Stock](https://raw.githubusercontent.com/jeffersongoncalves/laravel-erp-stock/main/art/jeffersongoncalves-laravel-erp-stock.png)

</div>

# Laravel ERP Stock

ERP stock — items, warehouses, stock entries and the perpetual inventory ledger for the Laravel ERP ecosystem.

This package is the inventory / stock module of the Laravel ERP ecosystem. It sits on top of the accounting layer: every stock movement is valued and posted to the general ledger. It depends on [`jeffersongoncalves/laravel-erp-core`](https://github.com/jeffersongoncalves/laravel-erp-core) and [`jeffersongoncalves/laravel-erp-accounting`](https://github.com/jeffersongoncalves/laravel-erp-accounting).

## Features

- **Item & Warehouse masters** — Items (with per-item valuation method, batch/serial flags, brand, default warehouse), a tree of warehouses each carrying its own inventory GL account, price lists, item prices, batches and serial numbers
- **Perpetual Inventory Engine** — A single `StockLedgerService` writes an immutable `StockLedgerEntry` per (item, warehouse) movement, keeps the live `Bin` balance in step and posts the net change in stock value to the general ledger
- **Two valuation methods** — **Moving Average** (a running weighted-average rate per bin) and **FIFO** (an ordered queue of `(qty, rate)` layers persisted on the bin)
- **Transaction Documents** — Stock entries (receipt / issue / transfer / manufacture / repack / subcontract), material requests, delivery notes, purchase receipts and stock reconciliations, all built on the core `IsSubmittable` lifecycle (`Draft → Submitted → Cancelled`)
- **Immutable Ledger** — `stock_ledger_entries` rows can never have their quantity or value impact edited or be deleted; cancellation flags the originals, writes reversing mirrors and recomputes the bin
- **Negative stock guard** — Outbound movements that would overdraw a bin throw a `DomainException` unless `allow_negative_stock` is enabled
- **Customizable Models** — Override any model via config (ModelResolver pattern); `Item`, `Warehouse`, `StockLedgerEntry` and `Bin` ship swappable contracts
- **Translations** — English and Brazilian Portuguese

## Compatibility

| Package | PHP | Laravel |
|---------|-----|---------|
| `^1.0`  | `^8.2` | `^11.0 \| ^12.0 \| ^13.0` |

## Installation

```bash
composer require jeffersongoncalves/laravel-erp-stock
```

Publish and run the migrations (the core and accounting package migrations must be published too):

```bash
php artisan vendor:publish --tag="erp-core-migrations"
php artisan vendor:publish --tag="erp-accounting-migrations"
php artisan vendor:publish --tag="erp-stock-migrations"
php artisan migrate
```

Publish the config (optional):

```bash
php artisan vendor:publish --tag="erp-stock-config"
```

## The Stock Ledger

`StockLedgerService` is the heart of the module and is registered as a singleton, with the accounting `GeneralLedgerService` injected.

```php
use JeffersonGoncalves\Erp\Stock\Services\StockLedgerService;

app(StockLedgerService::class)->post($voucher, [
    ['item_id' => $item->id, 'warehouse_id' => $wh->id, 'actual_qty' => 10, 'incoming_rate' => 5],
    ['item_id' => $item->id, 'warehouse_id' => $wh->id, 'actual_qty' => -3],
]);

app(StockLedgerService::class)->reverse($voucher);
```

Each movement is signed: a positive `actual_qty` is inbound (valued at `incoming_rate`), a negative one is outbound (valued by the item's valuation method). A document implementing `PostsToLedger` calls these hooks automatically on `submit()` / `cancel()`, and exposes the two GL accounts the value posts against through `stockGlAccounts()` (`PostsStockLedger`).

### Valuation

- **Moving Average** — `new_value = old_value + actual_qty * incoming_rate` on inbound; outbound consumes at the current `valuation_rate`; `valuation_rate = stock_value / qty_after` (guarded against divide-by-zero).
- **FIFO** — inbound pushes a `(qty, rate)` layer onto the queue persisted as JSON on the bin; outbound consumes the oldest layers first and is valued at `consumed_value / qty`.

### Posting logic

- **Purchase Receipt** — inbound at the receipt rate; GL **debit** the inventory account / **credit** Stock Received But Not Billed.
- **Delivery Note** — outbound consuming at valuation; GL **debit** Cost of Goods Sold / **credit** the inventory account.
- **Stock Entry** — receipt is inbound to the target warehouse, issue is outbound from the source warehouse, transfer is both (a same-value transfer nets to zero GL impact).
- **Stock Reconciliation** — posts the delta between the counted target quantity and the current bin quantity.

## Database Tables

All tables use the configured prefix shared across the ERP ecosystem (default: `erp_`): `warehouses`, `items`, `price_lists`, `item_prices`, `batches`, `serial_nos`, `stock_ledger_entries`, `bins`, `stock_entries`, `stock_entry_details`, `material_requests`, `material_request_items`, `delivery_notes`, `delivery_note_items`, `purchase_receipts`, `purchase_receipt_items`, `stock_reconciliations`, `stock_reconciliation_items`.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Jefferson Simão Gonçalves](https://github.com/jeffersongoncalves)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
