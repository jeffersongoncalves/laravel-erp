<?php

namespace JeffersonGoncalves\Erp\Maintenance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Maintenance\Support\ModelResolver;

/**
 * @property int $id
 * @property int $maintenance_schedule_id
 * @property string $item_code
 * @property string|null $item_name
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 * @property string $periodicity
 * @property int $no_of_visits
 * @property string|null $sales_person
 * @property string|null $serial_no
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read MaintenanceSchedule|null $maintenanceSchedule
 */
class MaintenanceScheduleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_schedule_id',
        'item_code',
        'item_name',
        'start_date',
        'end_date',
        'periodicity',
        'no_of_visits',
        'sales_person',
        'serial_no',
    ];

    protected $attributes = [
        'periodicity' => 'Monthly',
        'no_of_visits' => 1,
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'no_of_visits' => 'integer',
    ];

    public function getTable(): string
    {
        return (config('erp-maintenance.table_prefix') ?? '').'maintenance_schedule_items';
    }

    public function maintenanceSchedule(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::maintenanceSchedule(), 'maintenance_schedule_id');
    }
}
