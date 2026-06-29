<div class="filament-hidden">

![Laravel ERP Core](https://raw.githubusercontent.com/jeffersongoncalves/laravel-erp-core/main/art/jeffersongoncalves-laravel-erp-core.png)

</div>

# Laravel ERP Core

ERP core — companies, currencies, units, and the submittable-document foundation for the Laravel ERP ecosystem.

This package is the foundation of the Laravel ERP ecosystem. It ships the master-data Eloquent models, migrations, enums, and the reusable "submittable document" concerns (`IsSubmittable`, `HasNamingSeries`, `HasCompany`) that every other `erp-*` package builds upon.

## Features

- **Master Data Models** — Companies, currencies, currency exchange rates, units of measure, UOM conversions, fiscal years, departments, designations, brands, terms & conditions, addresses, and contacts
- **Submittable Documents** — `Draft → Submitted → Cancelled` lifecycle via the `IsSubmittable` concern, with immutability guarantees and ledger-posting hooks
- **Naming Series** — Pattern-based document identifiers (`SINV-.YYYY.-`) with safe, per-series incrementing counters
- **Polymorphic Addresses & Contacts** — Attach addresses and contacts to any model
- **Customizable Models** — Override any model via config while maintaining contract compliance (ModelResolver pattern)
- **Table Prefix** — Configurable table prefix to avoid naming collisions (default: `erp_`)
- **Contracts & Events** — `PostsToLedger`, `SubmittableDocument`, `DocumentSubmitted`, `DocumentCancelled`
- **Translations** — English and Brazilian Portuguese

## Compatibility

| Package | PHP | Laravel |
|---------|-----|---------|
| `^1.0`  | `^8.2` | `^11.0 \| ^12.0 \| ^13.0` |

## Installation

```bash
composer require jeffersongoncalves/laravel-erp-core
```

Publish and run the migrations:

```bash
php artisan vendor:publish --tag="erp-core-migrations"
php artisan migrate
```

Publish the config (optional):

```bash
php artisan vendor:publish --tag="erp-core-config"
```

## Submittable Documents

Add the `IsSubmittable` concern to any model with a `docstatus` column cast to `DocStatus`:

```php
use Illuminate\Database\Eloquent\Model;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;

class SalesInvoice extends Model implements SubmittableDocument
{
    use IsSubmittable;

    protected $casts = ['docstatus' => DocStatus::class];
}

$invoice->submit();   // Draft → Submitted, dispatches DocumentSubmitted
$invoice->cancel();   // Submitted → Cancelled, dispatches DocumentCancelled
```

A submitted document is immutable: any update (other than the controlled transition to cancelled) or delete throws a `DomainException`. If the model also implements `PostsToLedger`, `postLedgerEntries()` is called on submit and `reverseLedgerEntries()` on cancel.

## Naming Series

```php
use JeffersonGoncalves\Erp\Core\Concerns\HasNamingSeries;

class SalesInvoice extends Model
{
    use HasNamingSeries;

    protected function namingSeriesPattern(): ?string
    {
        return 'SINV-.YYYY.-';
    }
}

// On create with an empty `name`: SINV-2026-00001, SINV-2026-00002, ...
```

## Database Tables

All tables use the configured prefix (default: `erp_`):

| Table | Description |
|-------|-------------|
| `erp_companies` | Companies (with group hierarchy) |
| `erp_currencies` | Currencies |
| `erp_currency_exchanges` | Currency exchange rates |
| `erp_uoms` | Units of measure |
| `erp_uom_conversions` | UOM conversion factors |
| `erp_fiscal_years` | Fiscal years |
| `erp_departments` | Departments (with hierarchy) |
| `erp_designations` | Designations |
| `erp_brands` | Brands |
| `erp_terms_and_conditions` | Terms & conditions templates |
| `erp_addresses` | Polymorphic addresses |
| `erp_contacts` | Polymorphic contacts |
| `erp_naming_series` | Naming-series counters |

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
