<div class="filament-hidden">

![Laravel ERP Projects](https://raw.githubusercontent.com/jeffersongoncalves/laravel-erp-projects/main/art/jeffersongoncalves-laravel-erp-projects.png)

</div>

# Laravel ERP Projects

ERP projects — projects, tasks and timesheets for the Laravel ERP ecosystem.

This package is the projects / timesheet module of the Laravel ERP ecosystem. It owns projects, their task tree and the timesheets that capture effort, and bills billable timesheet hours into the accounting module as a sales invoice. It depends on [`jeffersongoncalves/laravel-erp-core`](https://github.com/jeffersongoncalves/laravel-erp-core) and [`jeffersongoncalves/laravel-erp-accounting`](https://github.com/jeffersongoncalves/laravel-erp-accounting).

## Features

- **Activity types** — A master of billable activities with default costing and billing rates.
- **Projects** — A status-driven document (`Open → Completed → Cancelled`) holding the customer party, schedule, percent complete and billable/billed totals.
- **Tasks** — A status-driven, self-referencing task tree with priority, schedule, progress and expected/actual time. Statuses: `Open`, `Working`, `Pending Review`, `Overdue`, `Completed`, `Cancelled`.
- **Timesheets** — A submittable document with billable/costing detail rows. While draft it recomputes its total hours, billable hours and billing/costing amounts from its lines.
- **Billing service** — `TimesheetService` turns a submitted timesheet into an accounting **sales invoice**, wiring the projects module into the general-ledger engine.
- **Effort documents** — Timesheets capture effort: submitting one posts nothing to the ledger. Revenue is recognised only when the timesheet is billed into a sales invoice.
- **Customizable Models** — Override any model via config (ModelResolver pattern); `Project` and `Task` ship swappable contracts.
- **Translations** — English and Brazilian Portuguese.

## Compatibility

| Package | PHP | Laravel |
|---------|-----|---------|
| `^1.0`  | `^8.2` | `^11.0 \| ^12.0 \| ^13.0` |

## Installation

```bash
composer require jeffersongoncalves/laravel-erp-projects
```

Publish and run the migrations (the core and accounting package migrations must be published too):

```bash
php artisan vendor:publish --tag="erp-core-migrations"
php artisan vendor:publish --tag="erp-accounting-migrations"
php artisan vendor:publish --tag="erp-projects-migrations"
php artisan migrate
```

Publish the config (optional):

```bash
php artisan vendor:publish --tag="erp-projects-config"
```

## Billing

`TimesheetService` is registered as a singleton.

```php
use JeffersonGoncalves\Erp\Projects\Services\TimesheetService;

// Timesheet -> Sales Invoice (draft; caller supplies the Receivable + income accounts)
$invoice = app(TimesheetService::class)->createSalesInvoice($timesheet, $receivable->id, $income->id);
$invoice->submit();
```

- **createSalesInvoice** copies the party from the timesheet's parent project onto an accounting `SalesInvoice`, then adds one invoice line per billable detail (`item_code` `SERVICE`, qty = hours, rate = billing rate, income account). The receivable (`debit_to`) is supplied by the caller because it is a non-nullable foreign key. The invoice is saved as a draft; submitting it posts the balanced receivable/income general-ledger entries. The timesheet is flagged billed (`sales_invoice_id`, `per_billed = 100`).

## Database Tables

All tables use the configured prefix shared across the ERP ecosystem (default: `erp_`): `activity_types`, `projects`, `tasks`, `timesheets`, `timesheet_details`.

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
