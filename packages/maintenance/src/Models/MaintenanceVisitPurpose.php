<?php

namespace JeffersonGoncalves\Erp\Maintenance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Maintenance\Enums\MaintenanceVisitPurposeType;
use JeffersonGoncalves\Erp\Maintenance\Support\ModelResolver;

/**
 * @property int $id
 * @property int $maintenance_visit_id
 * @property string|null $item_code
 * @property string|null $serial_no
 * @property string|null $description
 * @property string|null $work_done
 * @property string|null $service_person
 * @property MaintenanceVisitPurposeType $purpose_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read MaintenanceVisit|null $maintenanceVisit
 */
class MaintenanceVisitPurpose extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_visit_id',
        'item_code',
        'serial_no',
        'description',
        'work_done',
        'service_person',
        'purpose_type',
    ];

    protected $attributes = [
        'purpose_type' => 'Maintenance',
    ];

    protected $casts = [
        'purpose_type' => MaintenanceVisitPurposeType::class,
    ];

    protected static function booted(): void
    {
        static::saved(function (MaintenanceVisitPurpose $purpose): void {
            $purpose->maintenanceVisit?->recomputeCompletionStatus();
        });

        static::deleted(function (MaintenanceVisitPurpose $purpose): void {
            $purpose->maintenanceVisit?->recomputeCompletionStatus();
        });
    }

    public function getTable(): string
    {
        return (config('erp-maintenance.table_prefix') ?? '').'maintenance_visit_purposes';
    }

    public function maintenanceVisit(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::maintenanceVisit(), 'maintenance_visit_id');
    }
}
