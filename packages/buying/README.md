<div class="filament-hidden">

![Laravel ERP Buying](https://raw.githubusercontent.com/jeffersongoncalves/laravel-erp-buying/main/art/jeffersongoncalves-laravel-erp-buying.png)

</div>

# Laravel ERP Buying

ERP buying — suppliers, RFQs, supplier quotations and purchase orders for the Laravel ERP ecosystem.

This package is the buying/procurement module of the Laravel ERP ecosystem. It builds on
[`laravel-erp-core`](https://github.com/jeffersongoncalves/laravel-erp-core),
[`laravel-erp-accounting`](https://github.com/jeffersongoncalves/laravel-erp-accounting) and
[`laravel-erp-stock`](https://github.com/jeffersongoncalves/laravel-erp-stock).

## Features

- **Masters** — supplier groups (hierarchical) and suppliers, with polymorphic addresses and contacts from core.
- **Request for Quotation** — submittable RFQs with item lines and invited suppliers.
- **Supplier Quotation** — submittable quotations with item lines and live totals.
- **Purchase Order** — submittable orders with per-received / per-billed tracking and order status.
- **Conversions** — turn a Purchase Order into a stock Purchase Receipt or an accounting Purchase Invoice,
  and turn a Supplier Quotation into a Purchase Order.

Buying documents are commitments: they follow the submit/cancel lifecycle but do not post
to the general or stock ledger themselves. Inventory and accounting impact is realised only when the
downstream Purchase Receipt or Purchase Invoice is submitted.

## Installation

```bash
composer require jeffersongoncalves/laravel-erp-buying
```

Publish and run the migrations:

```bash
php artisan vendor:publish --tag="erp-buying-migrations"
php artisan migrate
```

Optionally publish the config:

```bash
php artisan vendor:publish --tag="erp-buying-config"
```

## Conversions

```php
use JeffersonGoncalves\Erp\Buying\Services\PurchaseOrderService;
use JeffersonGoncalves\Erp\Buying\Services\SupplierQuotationService;

$receipt = app(PurchaseOrderService::class)->createPurchaseReceipt($purchaseOrder);
$invoice = app(PurchaseOrderService::class)->createPurchaseInvoice($purchaseOrder);
$order = app(SupplierQuotationService::class)->createPurchaseOrder($supplierQuotation);
```

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
