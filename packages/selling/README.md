<div class="filament-hidden">

![Laravel ERP Selling](https://raw.githubusercontent.com/jeffersongoncalves/laravel-erp-selling/main/art/jeffersongoncalves-laravel-erp-selling.png)

</div>

# Laravel ERP Selling

ERP selling — customers, quotations and sales orders for the Laravel ERP ecosystem.

This package is the selling / sales module of the Laravel ERP ecosystem. It owns the customer master and the pre-sales documents (quotations and sales orders) and converts them into the downstream stock and accounting documents. It depends on [`jeffersongoncalves/laravel-erp-core`](https://github.com/jeffersongoncalves/laravel-erp-core), [`jeffersongoncalves/laravel-erp-accounting`](https://github.com/jeffersongoncalves/laravel-erp-accounting) and [`jeffersongoncalves/laravel-erp-stock`](https://github.com/jeffersongoncalves/laravel-erp-stock).

## Features

- **Customer masters** — Customers (with customer group, territory, type, default currency, default price list, credit limit and tax id), a tree of customer groups, sales partners and product bundles. Customers reuse the core address/contact morphs.
- **Quotations** — A submittable pre-sales document with line items and its own workflow status (`Draft → Open → Ordered → Lost → Expired → Cancelled`).
- **Sales Orders** — A submittable commitment with line items, delivery/billing progress (`per_delivered` / `per_billed`) and a status (`To Deliver and Bill`, `To Deliver`, `To Bill`, `Completed`, `Closed`).
- **Conversion services** — `QuotationService` turns an accepted quotation into a sales order; `SalesOrderService` turns a sales order into a stock **delivery note** or an accounting **sales invoice**, wiring the selling module into the stock and general-ledger engines.
- **Commitment documents** — Quotations and sales orders are commitments: submitting one posts nothing to the ledger. Stock and ledger impact happens only on conversion.
- **Customizable Models** — Override any model via config (ModelResolver pattern); `Customer` and `CustomerGroup` ship swappable contracts.
- **Translations** — English and Brazilian Portuguese.

## Compatibility

| Package | PHP | Laravel |
|---------|-----|---------|
| `^1.0`  | `^8.2` | `^11.0 \| ^12.0 \| ^13.0` |

## Installation

```bash
composer require jeffersongoncalves/laravel-erp-selling
```

Publish and run the migrations (the core, accounting and stock package migrations must be published too):

```bash
php artisan vendor:publish --tag="erp-core-migrations"
php artisan vendor:publish --tag="erp-accounting-migrations"
php artisan vendor:publish --tag="erp-stock-migrations"
php artisan vendor:publish --tag="erp-selling-migrations"
php artisan migrate
```

Publish the config (optional):

```bash
php artisan vendor:publish --tag="erp-selling-config"
```

## Conversion

`SalesOrderService` and `QuotationService` are registered as singletons.

```php
use JeffersonGoncalves\Erp\Selling\Services\QuotationService;
use JeffersonGoncalves\Erp\Selling\Services\SalesOrderService;

// Quotation -> Sales Order (the quotation is marked Ordered)
$salesOrder = app(QuotationService::class)->createSalesOrder($quotation);

// Sales Order -> Delivery Note (draft; set the COGS account and submit to post SLE + GL)
$deliveryNote = app(SalesOrderService::class)->createDeliveryNote($salesOrder);
$deliveryNote->counterAccountId = $cogsAccount->id;
$deliveryNote->submit();

// Sales Order -> Sales Invoice (draft; caller supplies the Receivable + income accounts)
$invoice = app(SalesOrderService::class)->createSalesInvoice($salesOrder, $receivable->id, $income->id);
$invoice->submit();
```

- **createDeliveryNote** copies the party/company onto a stock `DeliveryNote`, then for each order line still to deliver resolves the stock `Item` by `item_code` and adds a delivery line (`item_id`, remaining qty, rate, warehouse). Saved as a draft; submitting it posts the stock ledger entries and the COGS general-ledger pair.
- **createSalesInvoice** copies the party/company onto an accounting `SalesInvoice`, then for each order line still to bill adds an invoice line (`item_code`, remaining qty, rate, income account). Saved as a draft; submitting it posts the balanced receivable/income general-ledger entries.

## Database Tables

All tables use the configured prefix shared across the ERP ecosystem (default: `erp_`): `customer_groups`, `customers`, `sales_partners`, `product_bundles`, `product_bundle_items`, `quotations`, `quotation_items`, `sales_orders`, `sales_order_items`.

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
