<?php

use JeffersonGoncalves\Erp\Accounting\Enums\AccountType;
use JeffersonGoncalves\Erp\Accounting\Enums\RootType;
use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Accounting\Models\SalesInvoice;
use JeffersonGoncalves\Erp\Accounting\Services\GeneralLedgerService;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Projects\Models\ActivityType;
use JeffersonGoncalves\Erp\Projects\Models\Project;
use JeffersonGoncalves\Erp\Projects\Models\Timesheet;
use JeffersonGoncalves\Erp\Projects\Services\TimesheetService;

it('bills a timesheet into an accounting sales invoice and posts the general ledger', function () {
    $company = Company::factory()->create();
    $receivable = Account::factory()->ofType(RootType::Asset, AccountType::Receivable)->create(['company_id' => $company->id]);
    $income = Account::factory()->ofType(RootType::Income, AccountType::Income)->create(['company_id' => $company->id]);

    $project = Project::factory()->create([
        'company_id' => $company->id,
        'customer_name' => 'Acme Corp',
    ]);

    $development = ActivityType::factory()->create(['name' => 'Development']);
    $support = ActivityType::factory()->create(['name' => 'Support']);

    $timesheet = Timesheet::factory()->create([
        'company_id' => $company->id,
        'parent_project_id' => $project->id,
    ]);

    $timesheet->details()->create([
        'activity_type_id' => $development->id,
        'hours' => 4,
        'is_billable' => true,
        'billing_rate' => 100,
        'costing_rate' => 40,
    ]);

    $timesheet->details()->create([
        'activity_type_id' => $support->id,
        'hours' => 2,
        'is_billable' => true,
        'billing_rate' => 50,
        'costing_rate' => 20,
    ]);

    // A non-billable line must not produce an invoice line.
    $timesheet->details()->create([
        'hours' => 1,
        'is_billable' => false,
        'billing_rate' => 100,
        'costing_rate' => 40,
    ]);

    $timesheet->refresh();
    $timesheet->submit();

    $invoice = app(TimesheetService::class)->createSalesInvoice($timesheet, $receivable->id, $income->id);

    expect($invoice)->toBeInstanceOf(SalesInvoice::class)
        ->and($invoice->exists)->toBeTrue()
        ->and($invoice->customer_name)->toBe('Acme Corp')
        ->and($invoice->items)->toHaveCount(2);

    $first = $invoice->items->first();

    expect($first->item_code)->toBe('SERVICE')
        ->and($first->item_name)->toBe('Development')
        ->and($first->qty)->toBe(4.0)
        ->and($first->rate)->toBe(100.0)
        ->and($first->amount)->toBe(400.0)
        ->and($invoice->grand_total)->toBe(500.0);

    // The timesheet records the generated invoice and is flagged fully billed.
    $timesheet->refresh();

    expect($timesheet->sales_invoice_id)->toBe($invoice->id)
        ->and($timesheet->per_billed)->toBe(100.0);

    // Submitting the generated invoice posts the balanced ledger entries.
    $invoice->submit();

    $ledger = app(GeneralLedgerService::class);

    expect($ledger->accountBalance($receivable))->toBe(500.0)
        ->and($ledger->accountBalance($income))->toBe(-500.0);
});
