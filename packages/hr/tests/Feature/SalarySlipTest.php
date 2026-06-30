<?php

use JeffersonGoncalves\Erp\Accounting\Enums\AccountType;
use JeffersonGoncalves\Erp\Accounting\Enums\RootType;
use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Accounting\Services\GeneralLedgerService;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Hr\Models\Employee;
use JeffersonGoncalves\Erp\Hr\Models\SalaryComponent;
use JeffersonGoncalves\Erp\Hr\Models\SalarySlip;
use JeffersonGoncalves\Erp\Hr\Models\SalaryStructure;
use JeffersonGoncalves\Erp\Hr\Models\SalaryStructureAssignment;
use JeffersonGoncalves\Erp\Hr\Services\SalarySlipService;

function makePayrollFixture(): array
{
    $company = Company::factory()->create();

    $salaryExpense = Account::factory()->ofType(RootType::Expense, AccountType::ExpenseAccount)->create(['company_id' => $company->id]);
    $taxPayable = Account::factory()->ofType(RootType::Liability, AccountType::Payable)->create(['company_id' => $company->id]);
    $netPayable = Account::factory()->ofType(RootType::Liability, AccountType::Payable)->create(['company_id' => $company->id]);

    $basic = SalaryComponent::factory()->earning()->create(['account_id' => $salaryExpense->id]);
    $tax = SalaryComponent::factory()->deduction()->create(['account_id' => $taxPayable->id]);

    $structure = SalaryStructure::factory()->create(['company_id' => $company->id]);
    $structure->components()->create(['salary_component_id' => $basic->id, 'amount' => 5000]);
    $structure->components()->create(['salary_component_id' => $tax->id, 'amount' => 500]);

    $employee = Employee::factory()->create([
        'company_id' => $company->id,
        'date_of_joining' => '2020-01-01',
    ]);

    SalaryStructureAssignment::factory()->create([
        'employee_id' => $employee->id,
        'salary_structure_id' => $structure->id,
        'from_date' => '2020-01-01',
        'base' => 5000,
    ]);

    return [
        'company' => $company,
        'employee' => $employee,
        'structure' => $structure,
        'salaryExpense' => $salaryExpense,
        'taxPayable' => $taxPayable,
        'netPayable' => $netPayable,
    ];
}

it('builds a salary slip from the active assignment computing gross, deduction and net', function () {
    $fixture = makePayrollFixture();

    $slip = SalarySlip::factory()->create([
        'employee_id' => $fixture['employee']->id,
        'company_id' => $fixture['company']->id,
        'start_date' => '2024-12-01',
        'end_date' => '2024-12-31',
        'posting_date' => '2024-12-31',
    ]);

    app(SalarySlipService::class)->buildFromAssignment($slip);
    $slip->refresh();

    expect($slip->components)->toHaveCount(2)
        ->and($slip->gross_pay)->toBe(5000.0)
        ->and($slip->total_deduction)->toBe(500.0)
        ->and($slip->net_pay)->toBe(4500.0)
        ->and($slip->salary_structure_id)->toBe($fixture['structure']->id);
});

it('posts a balanced general ledger entry when a salary slip is submitted', function () {
    $fixture = makePayrollFixture();

    $slip = SalarySlip::factory()->create([
        'employee_id' => $fixture['employee']->id,
        'company_id' => $fixture['company']->id,
        'start_date' => '2024-12-01',
        'end_date' => '2024-12-31',
        'posting_date' => '2024-12-31',
    ]);

    app(SalarySlipService::class)->buildFromAssignment($slip);
    $slip->refresh();

    $slip->payableAccountId = $fixture['netPayable']->id;
    $slip->submit();

    $ledger = app(GeneralLedgerService::class);

    expect($slip->docstatus)->toBe(DocStatus::Submitted)
        ->and($ledger->accountBalance($fixture['salaryExpense']))->toBe(5000.0)
        ->and($ledger->accountBalance($fixture['taxPayable']))->toBe(-500.0)
        ->and($ledger->accountBalance($fixture['netPayable']))->toBe(-4500.0);
});

it('reverses the general ledger entries when a salary slip is cancelled', function () {
    $fixture = makePayrollFixture();

    $slip = SalarySlip::factory()->create([
        'employee_id' => $fixture['employee']->id,
        'company_id' => $fixture['company']->id,
        'start_date' => '2024-12-01',
        'end_date' => '2024-12-31',
        'posting_date' => '2024-12-31',
    ]);

    app(SalarySlipService::class)->buildFromAssignment($slip);
    $slip->refresh();

    $slip->payableAccountId = $fixture['netPayable']->id;
    $slip->submit();
    $slip->cancel();

    $ledger = app(GeneralLedgerService::class);

    expect($slip->docstatus)->toBe(DocStatus::Cancelled)
        ->and($ledger->accountBalance($fixture['salaryExpense']))->toBe(0.0)
        ->and($ledger->accountBalance($fixture['netPayable']))->toBe(0.0);
});

it('throws when submitting a salary slip without a payable account', function () {
    $fixture = makePayrollFixture();

    $slip = SalarySlip::factory()->create([
        'employee_id' => $fixture['employee']->id,
        'company_id' => $fixture['company']->id,
        'start_date' => '2024-12-01',
        'end_date' => '2024-12-31',
        'posting_date' => '2024-12-31',
    ]);

    app(SalarySlipService::class)->buildFromAssignment($slip);
    $slip->refresh();

    expect(fn () => $slip->submit())->toThrow(DomainException::class);
});
