<div class="filament-hidden">

![Laravel ERP Manufacturing](https://raw.githubusercontent.com/jeffersongoncalves/laravel-erp-manufacturing/main/art/jeffersongoncalves-laravel-erp-manufacturing.png)

</div>

# Laravel ERP Manufacturing

ERP manufacturing — BOMs, work orders and job cards for the Laravel ERP ecosystem.

This package is the manufacturing module of the Laravel ERP ecosystem. It sits on top of the stock layer: producing a finished good consumes raw materials and receives the output through the perpetual inventory engine, which values every movement and posts it to the general ledger. It depends on [`jeffersongoncalves/laravel-erp-core`](https://github.com/jeffersongoncalves/laravel-erp-core), [`jeffersongoncalves/laravel-erp-accounting`](https://github.com/jeffersongoncalves/laravel-erp-accounting) and [`jeffersongoncalves/laravel-erp-stock`](https://github.com/jeffersongoncalves/laravel-erp-stock).

## Features

- **Bills of Materials** — A `Bom` master with raw-material lines and operation lines; the raw-material, operating and total cost columns are recomputed automatically from the child rows whenever a line changes
- **Workstations & Operations** — Workstation masters (carrying an hourly rate and a production capacity) and the operations performed on them
- **Routings** — Reusable, ordered sequences of operations that can drive a BOM's process
- **Work Orders** — Submittable documents (`Draft → Submitted → Cancelled`) to produce a quantity of a finished item against a BOM; on submit the required-materials rows are populated from the BOM scaled to the ordered quantity and the status advances to *Not Started*
- **Job Cards** — Shop-floor execution of a work order's operations, with time logs
- **Manufacture Stock Entry** — `WorkOrderService::manufacture()` turns a work order into a `Manufacture` stock entry: an outbound consume line per raw material and one inbound finished-good line; submitting it runs the stock engine (outbound raw SLE + inbound FG SLE + GL impact)
- **Customizable Models** — Override any model via config (ModelResolver pattern)
- **Translations** — English and Brazilian Portuguese

## Compatibility

| Package | PHP | Laravel |
|---------|-----|---------|
| `^1.0`  | `^8.2` | `^11.0 \| ^12.0 \| ^13.0` |

## Installation

```bash
composer require jeffersongoncalves/laravel-erp-manufacturing
```

Publish and run the migrations (the core, accounting and stock package migrations must be published too):

```bash
php artisan vendor:publish --tag="erp-core-migrations"
php artisan vendor:publish --tag="erp-accounting-migrations"
php artisan vendor:publish --tag="erp-stock-migrations"
php artisan vendor:publish --tag="erp-manufacturing-migrations"
php artisan migrate
```

Publish the config (optional):

```bash
php artisan vendor:publish --tag="erp-manufacturing-config"
```

## Producing a Finished Good

A work order describes what to make; `WorkOrderService` turns it into the stock movement that actually consumes and produces.

```php
use JeffersonGoncalves\Erp\Manufacturing\Models\WorkOrder;
use JeffersonGoncalves\Erp\Manufacturing\Services\WorkOrderService;

$workOrder = WorkOrder::create([
    'production_item' => 'FG-WIDGET',
    'bom_id' => $bom->id,
    'qty' => 10,
    'company_id' => $company->id,
    'wip_warehouse_id' => $wip->id,
    'fg_warehouse_id' => $fg->id,
]);

// Required materials are pulled from the BOM (scaled to qty) and the status
// advances to "Not Started".
$workOrder->submit();

// Build the Manufacture stock entry: a consume line per raw material plus one
// finished-good line. It is returned in Draft.
$entry = app(WorkOrderService::class)->manufacture($workOrder);

// Submitting it runs the perpetual inventory engine: outbound raw stock-ledger
// entries, an inbound finished-good entry and the matching GL impact.
$entry->submit();
```

The produced quantity / status of the (now immutable) work order are intentionally left untouched by `manufacture()`; advancing them is a documented follow-up once a work order supports partial completion.

## Database Tables

All tables use the configured prefix shared across the ERP ecosystem (default: `erp_`): `workstations`, `operations`, `boms`, `bom_items`, `bom_operations`, `routings`, `routing_operations`, `work_orders`, `work_order_items`, `job_cards`, `job_card_time_logs`.

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
