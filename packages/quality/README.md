<div class="filament-hidden">

![Laravel ERP Quality](https://raw.githubusercontent.com/jeffersongoncalves/laravel-erp-quality/main/art/jeffersongoncalves-laravel-erp-quality.png)

</div>

# Laravel ERP Quality

ERP quality management — inspections, goals, procedures and non-conformances for the Laravel ERP ecosystem.

This package is the quality-management module of the Laravel ERP ecosystem. It owns the quality documents: quality goals and their objectives, quality procedures (a self-referencing tree of processes), quality inspection templates, quality inspections (with per-parameter readings), non-conformances, quality actions (corrective/preventive) and quality reviews. The inspected item and the source document (e.g. a Purchase Receipt or Delivery Note) are referenced as dynamic links (`item_code`, `reference_type`/`reference_name`), so the package depends only on [`jeffersongoncalves/laravel-erp-core`](https://github.com/jeffersongoncalves/laravel-erp-core) — there is no hard dependency on the stock or selling modules.

## Features

- **Quality masters** — Quality goals (with measurable objectives), quality procedures (a grouped, self-referencing tree of processes) and quality inspection templates (with numeric/min/max parameters).
- **Quality inspections** — An inspection against an item with a type (`Incoming`, `Outgoing`, `In Process`), a sample size, an optional template and a result (`Accepted`, `Rejected`). Each parameter reading has its own status, and the overall inspection status is recomputed to `Rejected` whenever any reading is `Rejected`.
- **Non-conformances** — A recorded quality issue tied to a procedure, with corrective and preventive actions and a status workflow (`Open`, `In Progress`, `Resolved`, `Closed`, `Cancelled`).
- **Quality actions** — Corrective/preventive actions with child resolutions and a status workflow (`Open`, `In Progress`, `Completed`, `Cancelled`).
- **Quality reviews** — A periodic review of a quality goal with achieved-vs-target objectives.
- **Customizable Models** — Override any model via config (ModelResolver pattern).
- **Translations** — English and Brazilian Portuguese.

## Compatibility

| Package | PHP | Laravel |
|---------|-----|---------|
| `^1.0`  | `^8.2` | `^11.0 \| ^12.0 \| ^13.0` |

## Installation

```bash
composer require jeffersongoncalves/laravel-erp-quality
```

Publish and run the migrations (the core package migrations must be published too):

```bash
php artisan vendor:publish --tag="erp-core-migrations"
php artisan vendor:publish --tag="erp-quality-migrations"
php artisan migrate
```

Publish the config (optional):

```bash
php artisan vendor:publish --tag="erp-quality-config"
```

## Dynamic Links

Quality inspections reference the inspected item through a plain `item_code` string and reference their source document (e.g. a Purchase Receipt or Delivery Note) through a dynamic link (`reference_type` + `reference_name`). This keeps the quality module decoupled from the stock and selling packages while still allowing those records to be resolved at the application layer.

## Database Tables

All tables use the configured prefix shared across the ERP ecosystem (default: `erp_`): `quality_goals`, `quality_goal_objectives`, `quality_procedures`, `quality_procedure_processes`, `quality_inspection_templates`, `quality_inspection_template_parameters`, `quality_inspections`, `quality_inspection_readings`, `non_conformances`, `quality_actions`, `quality_action_resolutions`, `quality_reviews`, `quality_review_objectives`.

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
