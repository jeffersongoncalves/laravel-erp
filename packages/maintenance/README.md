<div class="filament-hidden">

![Laravel ERP Maintenance](https://raw.githubusercontent.com/jeffersongoncalves/laravel-erp-maintenance/main/art/jeffersongoncalves-laravel-erp-maintenance.png)

</div>

# Laravel ERP Maintenance

ERP maintenance — maintenance schedules and visits for the Laravel ERP ecosystem.

This package is the maintenance module of the Laravel ERP ecosystem. It owns the maintenance documents: maintenance schedules (with their planned items and the generated dated visit rows) and maintenance visits (with their per-line service purposes). The customer, the serviced item and its serial number are referenced as dynamic links (`party_type`/`party_id`, `item_code`, `serial_no`), so the package depends only on [`jeffersongoncalves/laravel-erp-core`](https://github.com/jeffersongoncalves/laravel-erp-core) — there is no hard dependency on the selling, stock or CRM modules.

## Features

- **Maintenance schedules** — A submittable document that plans recurring maintenance for a customer. Each schedule item declares an item, a start date, a periodicity (`Weekly`, `Monthly`, `Quarterly`, `Yearly`) and a number of visits; submitting the schedule expands those into concrete dated visit rows.
- **Maintenance visits** — A submittable record of a service call with a type (`Scheduled`, `Unscheduled`, `Breakdown`) and a completion status (`Pending`, `Partially Completed`, `Fully Completed`). Each visit line (purpose) records the item, serial number, the work done and the service person; the overall completion status is recomputed from those lines.
- **Submittable documents** — Schedules and visits use the core `IsSubmittable` lifecycle (draft → submitted → cancelled) with naming series and company scoping.
- **Customizable Models** — Override any model via config (ModelResolver pattern).
- **Translations** — English and Brazilian Portuguese.

## Compatibility

| Package | PHP | Laravel |
|---------|-----|---------|
| `^1.0`  | `^8.2` | `^11.0 \| ^12.0 \| ^13.0` |

## Installation

```bash
composer require jeffersongoncalves/laravel-erp-maintenance
```

Publish and run the migrations (the core package migrations must be published too):

```bash
php artisan vendor:publish --tag="erp-core-migrations"
php artisan vendor:publish --tag="erp-maintenance-migrations"
php artisan migrate
```

Publish the config (optional):

```bash
php artisan vendor:publish --tag="erp-maintenance-config"
```

## Generating Visits

A maintenance schedule plans recurring work through its items. Submitting the schedule (or calling `MaintenanceScheduleService::generateSchedule()` directly) expands each item into `no_of_visits` dated rows in `maintenance_schedule_details`, stepping from the item's `start_date` by its periodicity (`Weekly` = +1 week, `Monthly` = +1 month, `Quarterly` = +3 months, `Yearly` = +1 year).

## Dynamic Links

Maintenance documents reference the customer through `party_type` + `party_id` and the serviced item through a plain `item_code` (and optional `serial_no`) string. This keeps the maintenance module decoupled from the selling, stock and CRM packages while still allowing those records to be resolved at the application layer.

## Database Tables

All tables use the configured prefix shared across the ERP ecosystem (default: `erp_`): `maintenance_schedules`, `maintenance_schedule_items`, `maintenance_schedule_details`, `maintenance_visits`, `maintenance_visit_purposes`.

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
