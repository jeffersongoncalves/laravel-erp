<?php

namespace JeffersonGoncalves\Erp\Hr\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\PostsToLedger;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Hr\Services\SalarySlipService;
use JeffersonGoncalves\Erp\Hr\Support\ModelResolver;

/**
 * @property int $id
 * @property int $employee_id
 * @property int|null $salary_structure_id
 * @property int|null $company_id
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 * @property Carbon|null $posting_date
 * @property float $gross_pay
 * @property float $total_deduction
 * @property float $net_pay
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Employee|null $employee
 * @property-read SalaryStructure|null $salaryStructure
 * @property-read Company|null $company
 * @property-read Collection<int, SalarySlipComponent> $components
 */
class SalarySlip extends Model implements PostsToLedger, SubmittableDocument
{
    use HasCompany;
    use HasFactory;
    use IsSubmittable;

    /**
     * Transient account the net pay is credited to when the slip posts to the
     * general ledger. It is supplied per submission rather than persisted,
     * because the payable account is a non-nullable posting target with no
     * sensible default.
     */
    public ?int $payableAccountId = null;

    protected $fillable = [
        'employee_id',
        'salary_structure_id',
        'company_id',
        'start_date',
        'end_date',
        'posting_date',
        'gross_pay',
        'total_deduction',
        'net_pay',
        'docstatus',
    ];

    protected $attributes = [
        'gross_pay' => 0,
        'total_deduction' => 0,
        'net_pay' => 0,
        'docstatus' => 0,
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'posting_date' => 'date',
        'gross_pay' => 'float',
        'total_deduction' => 'float',
        'net_pay' => 'float',
        'docstatus' => DocStatus::class,
    ];

    public function getTable(): string
    {
        return (config('erp-hr.table_prefix') ?? '').'salary_slips';
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::employee(), 'employee_id');
    }

    public function salaryStructure(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::salaryStructure(), 'salary_structure_id');
    }

    public function components(): HasMany
    {
        return $this->hasMany(ModelResolver::salarySlipComponent(), 'salary_slip_id');
    }

    /**
     * On submit a salary slip posts its earnings, deductions and net payable to
     * the general ledger through {@see SalarySlipService}.
     */
    public function postLedgerEntries(): void
    {
        app(SalarySlipService::class)->post($this, $this->payableAccountId);
    }

    public function reverseLedgerEntries(): void
    {
        app(SalarySlipService::class)->reverse($this);
    }
}
