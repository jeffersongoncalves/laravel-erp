<div class="filament-hidden">

![Laravel ERP HR](https://raw.githubusercontent.com/jeffersongoncalves/laravel-erp-hr/main/art/jeffersongoncalves-laravel-erp-hr.png)

</div>

# Laravel ERP HR

ERP HR & payroll — employees, attendance, leave, salary structures and payroll for the Laravel ERP ecosystem.

This package is the HR / payroll module of the Laravel ERP ecosystem. It owns the employee master, attendance and leave, the salary-component/structure catalog and the payroll run that produces salary slips, posting each slip's earnings and deductions into the accounting module's general ledger. It depends on [`jeffersongoncalves/laravel-erp-core`](https://github.com/jeffersongoncalves/laravel-erp-core) and [`jeffersongoncalves/laravel-erp-accounting`](https://github.com/jeffersongoncalves/laravel-erp-accounting).

## Features

- **Employees** — A master holding company, department, designation, joining/leaving dates, gender, status (`Active`, `Inactive`, `Left`) and CTC.
- **Attendance** — A submittable per-employee daily record (`Present`, `Absent`, `Half Day`, `On Leave`).
- **Leave** — Leave types (paid, allowance, negative-balance policy) and a submittable leave application that guards the available balance on submit.
- **Salary catalog** — Salary components (earning/deduction, taxable, default account) and a submittable salary structure with component lines, plus dated structure assignments per employee.
- **Salary slips** — A submittable document built from an employee's active structure assignment; it computes gross pay, total deduction and net pay, and on submit posts a **balanced general-ledger entry** (earnings debit their accounts, deductions and net pay credit theirs) via the accounting `GeneralLedgerService`. Cancelling reverses the ledger.
- **Payroll entry** — A submittable run that generates one draft salary slip per active assigned employee for a period.
- **Customizable Models** — Override any model via config (ModelResolver pattern); `Employee` and `SalaryStructure` ship swappable contracts.
- **Translations** — English and Brazilian Portuguese.

## Compatibility

| Package | PHP | Laravel |
|---------|-----|---------|
| `^1.0`  | `^8.2` | `^11.0 \| ^12.0 \| ^13.0` |

## Installation

```bash
composer require jeffersongoncalves/laravel-erp-hr
```

Publish and run the migrations (the core and accounting package migrations must be published too):

```bash
php artisan vendor:publish --tag="erp-core-migrations"
php artisan vendor:publish --tag="erp-accounting-migrations"
php artisan vendor:publish --tag="erp-hr-migrations"
php artisan migrate
```

Publish the config (optional):

```bash
php artisan vendor:publish --tag="erp-hr-config"
```

## Payroll

`SalarySlipService`, `PayrollEntryService` and `LeaveService` are resolved from the container.

```php
use JeffersonGoncalves\Erp\Hr\Services\PayrollEntryService;
use JeffersonGoncalves\Erp\Hr\Services\SalarySlipService;

// Generate draft salary slips for every active assigned employee in the period.
$slips = app(PayrollEntryService::class)->createSalarySlips($payrollEntry);

// A slip posts to the ledger on submit; the payable account is supplied by the
// caller because it is not stored on the slip.
$slip->payableAccountId = $payrollPayable->id;
$slip->submit(); // debits earning accounts, credits deduction + net-pay accounts
```

- **buildFromAssignment** pulls the employee's active salary-structure assignment, copies its component lines onto the slip and computes `gross_pay`, `total_deduction` and `net_pay`. Submitting posts the balanced general-ledger entry; cancelling reverses it. Submitting without a payable account throws.

## Database Tables

All tables use the configured prefix shared across the ERP ecosystem (default: `erp_`): `employees`, `leave_types`, `holiday_lists`, `holidays`, `salary_components`, `salary_structures`, `salary_structure_components`, `salary_structure_assignments`, `attendances`, `leave_applications`, `salary_slips`, `salary_slip_components`, `payroll_entries`.

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
