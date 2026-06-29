<?php

namespace JeffersonGoncalves\Erp\Maintenance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Maintenance\Enums\CompletionStatus;
use JeffersonGoncalves\Erp\Maintenance\Support\ModelResolver;

/**
 * @property int $id
 * @property int $maintenance_schedule_id
 * @property string $item_code
 * @property Carbon $scheduled_date
 * @property CompletionStatus $completion_status
 * @property string|null $serial_no
 * @property string|null $sales_person
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read MaintenanceSchedule|null $maintenanceSchedule
 */
class MaintenanceScheduleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_schedule_id',
        'item_code',
        'scheduled_date',
        'completion_status',
        'serial_no',
        'sales_person',
    ];

    protected $attributes = [
        'completion_status' => 'Pending',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completion_status' => CompletionStatus::class,
    ];

    public function getTable(): string
    {
        return (config('erp-maintenance.table_prefix') ?? '').'maintenance_schedule_details';
    }

    public function maintenanceSchedule(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::maintenanceSchedule(), 'maintenance_schedule_id');
    }
}
