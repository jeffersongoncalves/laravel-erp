<?php

namespace JeffersonGoncalves\Erp\Maintenance\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Concerns\HasNamingSeries;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\PostsToLedger;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Maintenance\Enums\CompletionStatus;
use JeffersonGoncalves\Erp\Maintenance\Enums\MaintenanceType;
use JeffersonGoncalves\Erp\Maintenance\Support\ModelResolver;

/**
 * @property int $id
 * @property string|null $naming_series
 * @property string $party_type
 * @property int|null $party_id
 * @property string|null $customer_name
 * @property int|null $maintenance_schedule_id
 * @property Carbon|null $mntc_date
 * @property MaintenanceType $maintenance_type
 * @property CompletionStatus $completion_status
 * @property string $status
 * @property int|null $company_id
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read MaintenanceSchedule|null $maintenanceSchedule
 * @property-read Collection<int, MaintenanceVisitPurpose> $purposes
 */
class MaintenanceVisit extends Model implements PostsToLedger, SubmittableDocument
{
    use HasCompany;
    use HasFactory;
    use HasNamingSeries;
    use IsSubmittable;

    protected $fillable = [
        'naming_series',
        'party_type',
        'party_id',
        'customer_name',
        'maintenance_schedule_id',
        'mntc_date',
        'maintenance_type',
        'completion_status',
        'status',
        'company_id',
        'docstatus',
    ];

    protected $attributes = [
        'party_type' => 'Customer',
        'maintenance_type' => 'Scheduled',
        'completion_status' => 'Pending',
        'status' => 'Draft',
        'docstatus' => 0,
    ];

    protected $casts = [
        'mntc_date' => 'date',
        'maintenance_type' => MaintenanceType::class,
        'completion_status' => CompletionStatus::class,
        'docstatus' => DocStatus::class,
    ];

    public function getTable(): string
    {
        return (config('erp-maintenance.table_prefix') ?? '').'maintenance_visits';
    }

    public function maintenanceSchedule(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::maintenanceSchedule(), 'maintenance_schedule_id');
    }

    public function purposes(): HasMany
    {
        return $this->hasMany(ModelResolver::maintenanceVisitPurpose(), 'maintenance_visit_id');
    }

    /**
     * Recompute the visit completion status from its purposes: it is Fully
     * Completed when every purpose has recorded work, Partially Completed when
     * only some have, and Pending while none do.
     */
    public function recomputeCompletionStatus(): void
    {
        $total = $this->purposes()->count();

        if ($total === 0) {
            return;
        }

        $done = $this->purposes()
            ->whereNotNull('work_done')
            ->where('work_done', '!=', '')
            ->count();

        $status = match (true) {
            $done === 0 => CompletionStatus::Pending,
            $done < $total => CompletionStatus::PartiallyCompleted,
            default => CompletionStatus::FullyCompleted,
        };

        if ($this->completion_status !== $status) {
            $this->completion_status = $status;
            $this->saveQuietly();
        }
    }

    /**
     * Maintenance visits record service work, not accounting movements:
     * submitting one posts nothing to the general ledger.
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
