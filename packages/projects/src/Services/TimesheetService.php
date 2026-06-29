<?php

namespace JeffersonGoncalves\Erp\Projects\Services;

use JeffersonGoncalves\Erp\Accounting\Models\SalesInvoice;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver as AccountingModelResolver;
use JeffersonGoncalves\Erp\Projects\Models\Timesheet;

/**
 * Bills a submitted timesheet into the accounting module by drafting a sales
 * invoice from its billable lines. The target invoice is resolved through the
 * accounting package's ModelResolver so it stays swappable.
 */
class TimesheetService
{
    /**
     * Build a draft sales invoice from a timesheet, copying the party from its
     * parent project and adding one invoice line per billable detail (item_code
     * `SERVICE`, qty = hours, rate = billing_rate). The receivable (debit_to)
     * and income accounts are supplied by the caller because debit_to is a
     * non-nullable foreign key; the invoice is saved as a draft and submitted
     * separately to post the general-ledger entries. The timesheet is flagged
     * as billed through the query builder, since a submitted timesheet is
     * immutable.
     */
    public function createSalesInvoice(Timesheet $timesheet, ?int $debitToId = null, ?int $incomeAccountId = null): SalesInvoice
    {
        $salesInvoiceClass = AccountingModelResolver::salesInvoice();

        $project = $timesheet->project;

        /** @var SalesInvoice $salesInvoice */
        $salesInvoice = new $salesInvoiceClass;
        $salesInvoice->fill([
            'party_type' => $project->party_type,
            'party_id' => $project->party_id,
            'customer_name' => $project->customer_name ?? '',
            'company_id' => $timesheet->company_id,
            'posting_date' => now(),
            'debit_to_id' => $debitToId,
        ]);
        $salesInvoice->save();

        foreach ($timesheet->details as $detail) {
            if (! $detail->is_billable) {
                continue;
            }

            $salesInvoice->items()->create([
                'item_code' => 'SERVICE',
                'item_name' => $detail->activityType?->name,
                'description' => $detail->description,
                'qty' => $detail->hours,
                'rate' => $detail->billing_rate,
                'income_account_id' => $incomeAccountId,
            ]);
        }

        // Billing fields are workflow data, not document data; update them
        // directly so a submitted (immutable) timesheet can still be flagged.
        $timesheet->newQuery()
            ->whereKey($timesheet->getKey())
            ->update([
                'sales_invoice_id' => $salesInvoice->getKey(),
                'per_billed' => 100,
            ]);

        $timesheet->refresh();

        return $salesInvoice->refresh();
    }
}
