<div class="filament-hidden">

![Laravel ERP Support](https://raw.githubusercontent.com/jeffersongoncalves/laravel-erp-support/main/art/jeffersongoncalves-laravel-erp-support.png)

</div>

# Laravel ERP Support

ERP support — issues, service level agreements and warranty claims for the Laravel ERP ecosystem.

This package is the support (helpdesk) module of the Laravel ERP ecosystem. It owns the post-sales service documents: support issues, the service level agreements (SLAs) that govern them, and warranty claims raised against sold serial numbers. The customer and serial number are referenced as dynamic links (`party_type`/`party_id`, `serial_no`), so the package depends only on [`jeffersongoncalves/laravel-erp-core`](https://github.com/jeffersongoncalves/laravel-erp-core) — there is no hard dependency on the selling or stock modules.

## Features

- **Support masters** — Issue types and service level agreements (with a default priority, enabled flag and per-priority response/resolution times).
- **Issues** — A support ticket with a subject, party (dynamic link), priority (`Low`, `Medium`, `High`, `Urgent`) and a status workflow (`Open`, `Replied`, `On Hold`, `Resolved`, `Closed`). Optionally tied to an issue type and a service level agreement, with an SLA `agreement_status` (`First Response Due`, `Resolution Due`, `Fulfilled`, `Failed`, `Paused`) and first-response/resolution timestamps.
- **Warranty claims** — A complaint raised against a sold serial number / item, with its own status workflow (`Open`, `In Progress`, `Resolved`, `Closed`, `Cancelled`) and resolution details.
- **Customizable Models** — Override any model via config (ModelResolver pattern).
- **Translations** — English and Brazilian Portuguese.

## Compatibility

| Package | PHP | Laravel |
|---------|-----|---------|
| `^1.0`  | `^8.2` | `^11.0 \| ^12.0 \| ^13.0` |

## Installation

```bash
composer require jeffersongoncalves/laravel-erp-support
```

Publish and run the migrations (the core package migrations must be published too):

```bash
php artisan vendor:publish --tag="erp-core-migrations"
php artisan vendor:publish --tag="erp-support-migrations"
php artisan migrate
```

Publish the config (optional):

```bash
php artisan vendor:publish --tag="erp-support-config"
```

## Dynamic Links

Issues and warranty claims reference their customer through a polymorphic-style dynamic link (`party_type` + `party_id`) and the warranty claim references the sold unit through a plain `serial_no` string. This keeps the support module decoupled from the selling and stock packages while still allowing those records to be resolved at the application layer.

## Database Tables

All tables use the configured prefix shared across the ERP ecosystem (default: `erp_`): `issue_types`, `service_level_agreements`, `service_level_priorities`, `issues`, `warranty_claims`.

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
