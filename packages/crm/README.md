<div class="filament-hidden">

![Laravel ERP CRM](https://raw.githubusercontent.com/jeffersongoncalves/laravel-erp-crm/main/art/jeffersongoncalves-laravel-erp-crm.png)

</div>

# Laravel ERP CRM

ERP CRM — leads, opportunities and campaigns for the Laravel ERP ecosystem.

This package is the CRM module of the Laravel ERP ecosystem. It owns the pre-sales relationship documents (leads, opportunities, campaigns, contracts and appointments) and converts them into the downstream selling documents. It depends on [`jeffersongoncalves/laravel-erp-core`](https://github.com/jeffersongoncalves/laravel-erp-core) and [`jeffersongoncalves/laravel-erp-selling`](https://github.com/jeffersongoncalves/laravel-erp-selling).

## Features

- **CRM masters** — Lead sources, campaigns and contracts (with party, signing status and contract terms).
- **Leads** — A relationship record with contact details and its own workflow status (`Lead → Open → Replied → Opportunity → Quotation → Converted`, plus `Do Not Contact`), tied to a lead source and campaign.
- **Opportunities** — A qualified deal with line items, amount, probability, type (`Sales`, `Support`, `Maintenance`) and a status (`Open`, `Quotation`, `Converted`, `Lost`, `Closed`, `Replied`).
- **Appointments** — Scheduled meetings with a party and contact details.
- **Conversion services** — `LeadService` promotes a lead into a selling **Customer** (flagging the lead `Converted`) or opens an **Opportunity** from it; `OpportunityService` turns an opportunity into a draft selling **Quotation**, copying every line.
- **Customizable Models** — Override any model via config (ModelResolver pattern).
- **Translations** — English and Brazilian Portuguese.

## Compatibility

| Package | PHP | Laravel |
|---------|-----|---------|
| `^1.0`  | `^8.2` | `^11.0 \| ^12.0 \| ^13.0` |

## Installation

```bash
composer require jeffersongoncalves/laravel-erp-crm
```

Publish and run the migrations (the core and selling package migrations must be published too):

```bash
php artisan vendor:publish --tag="erp-core-migrations"
php artisan vendor:publish --tag="erp-selling-migrations"
php artisan vendor:publish --tag="erp-crm-migrations"
php artisan migrate
```

Publish the config (optional):

```bash
php artisan vendor:publish --tag="erp-crm-config"
```

## Conversion

`LeadService` and `OpportunityService` are registered as singletons.

```php
use JeffersonGoncalves\Erp\Crm\Services\LeadService;
use JeffersonGoncalves\Erp\Crm\Services\OpportunityService;

// Lead -> Customer (the lead is flagged Converted and linked to the new customer)
$customer = app(LeadService::class)->createCustomer($lead);

// Lead -> Opportunity (the lead moves to the Opportunity stage)
$opportunity = app(LeadService::class)->createOpportunity($lead);

// Opportunity -> Quotation (draft; the opportunity moves to the Quotation stage)
$quotation = app(OpportunityService::class)->createQuotation($opportunity);
```

- **createCustomer** instantiates the selling `Customer` from the lead (using the company name when present), saves it, then links the lead back via `customer_id` and flags it `Converted`.
- **createOpportunity** opens a CRM `Opportunity` from the lead (`opportunity_from = Lead`) and moves the lead to the `Opportunity` stage.
- **createQuotation** copies the party/company onto a draft selling `Quotation` and copies every opportunity line into a quotation line, then moves the opportunity to the `Quotation` stage.

## Database Tables

All tables use the configured prefix shared across the ERP ecosystem (default: `erp_`): `lead_sources`, `campaigns`, `contracts`, `leads`, `opportunities`, `opportunity_items`, `appointments`.

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
