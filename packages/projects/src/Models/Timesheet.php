<?php

namespace JeffersonGoncalves\Erp\Projects\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver as AccountingModelResolver;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Concerns\HasNamingSeries;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\PostsToLedger;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Projects\Support\ModelResolver;

/**
 * @property int $id
 * @property string|null $naming_series
 * @property string|null $employee
 * @property string|null $user
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 * @property string $status
 * @property float $total_hours
 * @property float $total_billable_hours
 * @property float $total_billable_amount
 * @property float $total_costing_amount
 * @property float $per_billed
 * @property int|null $parent_project_id
 * @property int|null $sales_invoice_id
 * @property int|null $company_id
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Project|null $project
 * @property-read Collection<int, TimesheetDetail> $details
 */
class Timesheet extends Model implements PostsToLedger, SubmittableDocument
{
    use HasCompany;
    use HasFactory;
    use HasNamingSeries;
    use IsSubmittable;

    protected $fillable = [
        'naming_series',
        'employee',
        'user',
        'start_date',
        'end_date',
        'status',
        'total_hours',
        'total_billable_hours',
        'total_billable_amount',
        'total_costing_amount',
        'per_billed',
        'parent_project_id',
        'sales_invoice_id',
        'company_id',
        'docstatus',
    ];

    protected $attributes = [
        'status' => 'Draft',
        'total_hours' => 0,
        'total_billable_hours' => 0,
        'total_billable_amount' => 0,
        'total_costing_amount' => 0,
        'per_billed' => 0,
        'docstatus' => 0,
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_hours' => 'float',
        'total_billable_hours' => 'float',
        'total_billable_amount' => 'float',
        'total_costing_amount' => 'float',
        'per_billed' => 'float',
        'docstatus' => DocStatus::class,
    ];

    protected static function booted(): void
    {
        static::saving(function (Timesheet $timesheet): void {
            if ($timesheet->docstatus === DocStatus::Draft) {
                $timesheet->calculateTotals();
            }
        });
    }

    public function getTable(): string
    {
        return (config('erp-projects.table_prefix') ?? '').'timesheets';
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::project(), 'parent_project_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(ModelResolver::timesheetDetail(), 'timesheet_id');
    }

    public function salesInvoice(): BelongsTo
    {
        return $this->belongsTo(AccountingModelResolver::salesInvoice(), 'sales_invoice_id');
    }

    public function calculateTotals(): void
    {
        if (! $this->exists) {
            return;
        }

        $this->total_hours = (float) $this->details()->sum('hours');
        $this->total_billable_hours = (float) $this->details()->where('is_billable', true)->sum('hours');
        $this->total_billable_amount = (float) $this->details()->where('is_billable', true)->sum('billing_amount');
        $this->total_costing_amount = (float) $this->details()->sum('costing_amount');
    }

    /**
     * Timesheets capture effort, not accounting impact: submitting one posts
     * nothing to the general ledger. Revenue is recognised only when the
     * timesheet is billed into a sales invoice via {@see TimesheetService}.
     */
    public function postLedgerEntries(): void
    {
        // No ledger impact.
    }

    public function reverseLedgerEntries(): void
    {
        // No ledger impact.
    }
}
