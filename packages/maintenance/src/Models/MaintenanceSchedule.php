<?php

namespace JeffersonGoncalves\Erp\Maintenance\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Concerns\HasNamingSeries;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\PostsToLedger;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Maintenance\Services\MaintenanceScheduleService;
use JeffersonGoncalves\Erp\Maintenance\Support\ModelResolver;

/**
 * @property int $id
 * @property string|null $naming_series
 * @property string $party_type
 * @property int|null $party_id
 * @property string|null $customer_name
 * @property Carbon|null $transaction_date
 * @property string $status
 * @property int|null $company_id
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, MaintenanceScheduleItem> $items
 * @property-read Collection<int, MaintenanceScheduleDetail> $scheduleDetails
 * @property-read Collection<int, MaintenanceVisit> $visits
 */
class MaintenanceSchedule extends Model implements PostsToLedger, SubmittableDocument
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
        'transaction_date',
        'status',
        'company_id',
        'docstatus',
    ];

    protected $attributes = [
        'party_type' => 'Customer',
        'status' => 'Draft',
        'docstatus' => 0,
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'docstatus' => DocStatus::class,
    ];

    protected static function booted(): void
    {
        static::updated(function (MaintenanceSchedule $schedule): void {
            if ($schedule->wasChanged('docstatus') && $schedule->docstatus === DocStatus::Submitted) {
                app(MaintenanceScheduleService::class)->generateSchedule($schedule);
            }
        });
    }

    public function getTable(): string
    {
        return (config('erp-maintenance.table_prefix') ?? '').'maintenance_schedules';
    }

    public function items(): HasMany
    {
        return $this->hasMany(ModelResolver::maintenanceScheduleItem(), 'maintenance_schedule_id');
    }

    public function scheduleDetails(): HasMany
    {
        return $this->hasMany(ModelResolver::maintenanceScheduleDetail(), 'maintenance_schedule_id');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(ModelResolver::maintenanceVisit(), 'maintenance_schedule_id');
    }

    /**
     * Maintenance schedules plan future visits; submitting one generates the
     * dated visit rows but posts nothing to the general ledger.
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
