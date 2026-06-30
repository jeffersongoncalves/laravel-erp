<?php

namespace JeffersonGoncalves\Erp\Hr\Services;

use DomainException;
use Illuminate\Database\Eloquent\Model;
use JeffersonGoncalves\Erp\Accounting\Services\GeneralLedgerService;
use JeffersonGoncalves\Erp\Hr\Enums\SalaryComponentType;
use JeffersonGoncalves\Erp\Hr\Models\SalarySlip;
use JeffersonGoncalves\Erp\Hr\Support\ModelResolver;

/**
 * Builds salary slips from an employee's active salary structure assignment and
 * posts the resulting payroll to the accounting general ledger.
 *
 * Posting mirrors the assets module: the slip acts as the GL voucher
 * (voucherable morph), earnings debit their expense accounts, deductions credit
 * theirs, and the net pay credits a payable account supplied by the caller.
 */
class SalarySlipService
{
    /**
     * Populate a draft slip from the employee's active salary structure.
     *
     * Reads the latest assignment effective on or before the slip's end date,
     * copies each structure component into a slip component (carrying the
     * component's earning/deduction type), and stores the gross, deduction and
     * net totals on the slip.
     */
    public function buildFromAssignment(SalarySlip $slip): SalarySlip
    {
        if (! $slip->exists) {
            $slip->save();
        }

        $assignment = $this->activeAssignment($slip);

        if ($assignment === null) {
            throw new DomainException('Employee has no active salary structure assignment');
        }

        $structureId = (int) $assignment->getAttribute('salary_structure_id');

        $slip->components()->delete();

        $componentModel = ModelResolver::salaryComponent();

        $structureComponents = ModelResolver::salaryStructureComponent()::query()
            ->where('salary_structure_id', $structureId)
            ->get();

        $gross = 0.0;
        $deduction = 0.0;

        foreach ($structureComponents as $structureComponent) {
            $componentId = (int) $structureComponent->getAttribute('salary_component_id');
            $amount = (float) $structureComponent->getAttribute('amount');

            $component = $componentModel::query()->find($componentId);

            if ($component === null) {
                continue;
            }

            $type = $component->getAttribute('type');

            if (! $type instanceof SalaryComponentType) {
                continue;
            }

            $slip->components()->create([
                'salary_component_id' => $componentId,
                'type' => $type->value,
                'amount' => $amount,
            ]);

            if ($type === SalaryComponentType::Earning) {
                $gross += $amount;
            } else {
                $deduction += $amount;
            }
        }

        $slip->setAttribute('salary_structure_id', $structureId);
        $slip->setAttribute('gross_pay', $gross);
        $slip->setAttribute('total_deduction', $deduction);
        $slip->setAttribute('net_pay', $gross - $deduction);
        $slip->save();

        return $slip;
    }

    /**
     * Post a submitted slip to the general ledger.
     *
     * Each earning component debits its account, each deduction credits its
     * account and the net pay credits $payableAccountId. The accounting service
     * enforces that total debit equals total credit.
     */
    public function post(SalarySlip $slip, ?int $payableAccountId): void
    {
        if ($payableAccountId === null) {
            throw new DomainException('Salary slip requires a payable account to post');
        }

        $componentModel = ModelResolver::salaryComponent();

        $entries = [];

        foreach ($slip->components as $line) {
            $type = $line->getAttribute('type');
            $amount = (float) $line->getAttribute('amount');

            $component = $componentModel::query()->find($line->getAttribute('salary_component_id'));
            $accountId = $component?->getAttribute('account_id');

            if ($accountId === null) {
                throw new DomainException('Salary component is missing its account');
            }

            if ($type === SalaryComponentType::Earning) {
                $entries[] = [
                    'account_id' => $accountId,
                    'debit' => $amount,
                    'credit' => 0,
                    'remarks' => 'Salary',
                ];
            } else {
                $entries[] = [
                    'account_id' => $accountId,
                    'debit' => 0,
                    'credit' => $amount,
                    'remarks' => 'Salary deduction',
                ];
            }
        }

        $entries[] = [
            'account_id' => $payableAccountId,
            'debit' => 0,
            'credit' => (float) $slip->net_pay,
            'remarks' => 'Net pay',
        ];

        app(GeneralLedgerService::class)->post($slip, $entries);
    }

    /**
     * Reverse the ledger impact of a cancelled slip.
     */
    public function reverse(SalarySlip $slip): void
    {
        app(GeneralLedgerService::class)->reverse($slip);
    }

    /**
     * The latest salary structure assignment effective for the slip's period.
     */
    private function activeAssignment(SalarySlip $slip): ?Model
    {
        /** @var Model|null $assignment */
        $assignment = ModelResolver::salaryStructureAssignment()::query()
            ->where('employee_id', $slip->employee_id)
            ->where('from_date', '<=', $slip->end_date)
            ->orderByDesc('from_date')
            ->first();

        return $assignment;
    }
}
