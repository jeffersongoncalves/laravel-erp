<?php

namespace JeffersonGoncalves\Erp\Projects\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Projects\Support\ModelResolver;

/**
 * @property int $id
 * @property int $timesheet_id
 * @property int|null $activity_type_id
 * @property int|null $task_id
 * @property int|null $project_id
 * @property Carbon|null $from_time
 * @property Carbon|null $to_time
 * @property float $hours
 * @property bool $is_billable
 * @property float $billing_rate
 * @property float $billing_amount
 * @property float $costing_rate
 * @property float $costing_amount
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Timesheet|null $timesheet
 * @property-read ActivityType|null $activityType
 * @property-read Task|null $task
 * @property-read Project|null $project
 */
class TimesheetDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'timesheet_id',
        'activity_type_id',
        'task_id',
        'project_id',
        'from_time',
        'to_time',
        'hours',
        'is_billable',
        'billing_rate',
        'billing_amount',
        'costing_rate',
        'costing_amount',
        'description',
    ];

    protected $attributes = [
        'hours' => 0,
        'is_billable' => true,
        'billing_rate' => 0,
        'billing_amount' => 0,
        'costing_rate' => 0,
        'costing_amount' => 0,
    ];

    protected $casts = [
        'from_time' => 'datetime',
        'to_time' => 'datetime',
        'hours' => 'float',
        'is_billable' => 'boolean',
        'billing_rate' => 'float',
        'billing_amount' => 'float',
        'costing_rate' => 'float',
        'costing_amount' => 'float',
    ];

    protected static function booted(): void
    {
        static::saving(function (TimesheetDetail $detail): void {
            $detail->billing_amount = $detail->is_billable
                ? (float) $detail->hours * (float) $detail->billing_rate
                : 0.0;
            $detail->costing_amount = (float) $detail->hours * (float) $detail->costing_rate;
        });

        static::saved(fn (TimesheetDetail $detail) => $detail->syncParentTotals());
        static::deleted(fn (TimesheetDetail $detail) => $detail->syncParentTotals());
    }

    public function getTable(): string
    {
        return (config('erp-projects.table_prefix') ?? '').'timesheet_details';
    }

    public function timesheet(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::timesheet(), 'timesheet_id');
    }

    public function activityType(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::activityType(), 'activity_type_id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::task(), 'task_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::project(), 'project_id');
    }

    protected function syncParentTotals(): void
    {
        $parent = $this->timesheet;

        if ($parent === null || $parent->docstatus !== DocStatus::Draft) {
            return;
        }

        $parent->save();
    }
}
