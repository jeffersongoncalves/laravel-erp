# Laravel ERP

![Laravel ERP](https://raw.githubusercontent.com/jeffersongoncalves/laravel-erp/main/art/jeffersongoncalves-laravel-erp.png)

Development **monorepo** for the Laravel ERP domain ecosystem — a native, framework-agnostic ERP core in pure Laravel (models, migrations, services). No API client, no sync: real PHP domain logic.

> This repository is the **source of truth**. Each package under [`packages/`](packages) is automatically **split read-only** into its own repository (`jeffersongoncalves/laravel-erp-*`). Open issues and pull requests **here**, not on the mirrors.

## Packages

Install the whole domain at once:

```bash
composer require jeffersongoncalves/laravel-erp
```

…or pull only the modules you need:

| Package | Description |
|---------|-------------|
| [`laravel-erp-core`](https://github.com/jeffersongoncalves/laravel-erp-core) | Company, currency, UOM, address/contact + submittable-document foundation (docstatus, naming series, ledger contracts) |
| [`laravel-erp-accounting`](https://github.com/jeffersongoncalves/laravel-erp-accounting) | Chart of accounts, GL, journal/payment entries, sales/purchase invoices, taxes, banks |
| [`laravel-erp-stock`](https://github.com/jeffersongoncalves/laravel-erp-stock) | Items, warehouses, perpetual inventory (FIFO + moving average), stock ledger |
| [`laravel-erp-selling`](https://github.com/jeffersongoncalves/laravel-erp-selling) | Customers, quotations, sales orders + conversions to delivery/invoice |
| [`laravel-erp-buying`](https://github.com/jeffersongoncalves/laravel-erp-buying) | Suppliers, RFQ, supplier quotations, purchase orders + receipts/invoices |
| [`laravel-erp-manufacturing`](https://github.com/jeffersongoncalves/laravel-erp-manufacturing) | BOM, work orders, job cards, manufacture stock entries |
| [`laravel-erp-assets`](https://github.com/jeffersongoncalves/laravel-erp-assets) | Asset categories, depreciation (SL/WDV/DDB) posting to GL |
| [`laravel-erp-subcontracting`](https://github.com/jeffersongoncalves/laravel-erp-subcontracting) | Subcontracting BOM, orders, receipts |
| [`laravel-erp-crm`](https://github.com/jeffersongoncalves/laravel-erp-crm) | Leads, opportunities, campaigns, contracts |
| [`laravel-erp-projects`](https://github.com/jeffersongoncalves/laravel-erp-projects) | Projects, tasks, timesheets → sales invoice |
| [`laravel-erp-support`](https://github.com/jeffersongoncalves/laravel-erp-support) | Issues, SLAs, warranty claims |
| [`laravel-erp-quality`](https://github.com/jeffersongoncalves/laravel-erp-quality) | Quality goals/procedures, inspections, non-conformance |
| [`laravel-erp-maintenance`](https://github.com/jeffersongoncalves/laravel-erp-maintenance) | Maintenance schedules and visits |

## Compatibility

- **PHP** 8.2+
- **Laravel** 11.x · 12.x · 13.x

## Development

This is a Symplify monorepo. Everything runs once over `packages/`:

```bash
composer install
composer test       # Pest
composer analyse    # PHPStan level 5
composer format     # Pint
```

Packages are split to their read-only mirrors on every push via `symplify/monorepo-split-github-action`; tags propagate to publish releases on Packagist.

## License

The MIT License (MIT). Built by [Jefferson Gonçalves](https://github.com/jeffersongoncalves).
