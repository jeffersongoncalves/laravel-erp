<?php

namespace JeffersonGoncalves\Erp\Hr\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use JeffersonGoncalves\Erp\Hr\Enums\EmployeeStatus;
use JeffersonGoncalves\Erp\Hr\Models\PayrollEntry;
use JeffersonGoncalves\Erp\Hr\Models\SalarySlip;
use JeffersonGoncalves\Erp\Hr\Support\ModelResolver;

/**
 * Generates draft salary slips for every active employee covered by a payroll
 * entry's period.
 */
class PayrollEntryService
{
    /**
     * Create one draft salary slip per active employee of the entry's company
     * whose employment overlaps the payroll period and who has an active salary
     * structure assignment.
     *
     * @return Collection<int, SalarySlip>
     */
    public function createSalarySlips(PayrollEntry $entry): Collection
    {
        $assignmentModel = ModelResolver::salaryStructureAssignment();
        $slipModel = ModelResolver::salarySlip();
        $service = app(SalarySlipService::class);

        $employees = ModelResolver::employee()::query()
            ->where('company_id', $entry->company_id)
            ->where('status', EmployeeStatus::Active->value)
            ->whereDate('date_of_joining', '<=', $entry->end_date)
            ->where(function (Builder $query) use ($entry): void {
                $query->whereNull('date_of_leaving')
                    ->orWhereDate('date_of_leaving', '>=', $entry->start_date);
            })
            ->get();

        /** @var Collection<int, SalarySlip> $slips */
        $slips = new Collection;

        foreach ($employees as $employee) {
            $hasAssignment = $assignmentModel::query()
                ->where('employee_id', $employee->getKey())
                ->where('from_date', '<=', $entry->end_date)
                ->exists();

            if (! $hasAssignment) {
                continue;
            }

            $slip = new $slipModel;

            if (! $slip instanceof SalarySlip) {
                continue;
            }

            $slip->fill([
                'employee_id' => $employee->getKey(),
                'company_id' => $entry->company_id,
                'start_date' => $entry->start_date,
                'end_date' => $entry->end_date,
                'posting_date' => $entry->end_date,
            ]);
            $slip->save();

            $service->buildFromAssignment($slip);

            $slips->push($slip);
        }

        return $slips;
    }
}
