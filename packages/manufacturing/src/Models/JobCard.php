<?php

namespace JeffersonGoncalves\Erp\Manufacturing\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Concerns\HasNamingSeries;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Manufacturing\Enums\JobCardStatus;
use JeffersonGoncalves\Erp\Manufacturing\Support\ModelResolver;

/**
 * Tracks the execution of a single operation of a work order on the shop floor,
 * including the time logged against it.
 *
 * @property int $id
 * @property string|null $naming_series
 * @property int $work_order_id
 * @property int|null $operation_id
 * @property int|null $workstation_id
 * @property float $for_quantity
 * @property float $total_completed_qty
 * @property JobCardStatus $status
 * @property int|null $company_id
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read WorkOrder|null $workOrder
 * @property-read Operation|null $operation
 * @property-read Workstation|null $workstation
 * @property-read Collection<int, JobCardTimeLog> $timeLogs
 */
class JobCard extends Model implements SubmittableDocument
{
    use HasCompany;
    use HasFactory;
    use HasNamingSeries;
    use IsSubmittable;

    protected $fillable = [
        'naming_series',
        'work_order_id',
        'operation_id',
        'workstation_id',
        'for_quantity',
        'total_completed_qty',
        'status',
        'company_id',
        'docstatus',
    ];

    protected $attributes = [
        'for_quantity' => 0,
        'total_completed_qty' => 0,
        'status' => JobCardStatus::Open->value,
        'docstatus' => 0,
    ];

    protected $casts = [
        'for_quantity' => 'float',
        'total_completed_qty' => 'float',
        'status' => JobCardStatus::class,
        'docstatus' => DocStatus::class,
    ];

    public function getTable(): string
    {
        return (config('erp-manufacturing.table_prefix') ?? '').'job_cards';
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::workOrder(), 'work_order_id');
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::operation(), 'operation_id');
    }

    public function workstation(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::workstation(), 'workstation_id');
    }

    public function timeLogs(): HasMany
    {
        return $this->hasMany(ModelResolver::jobCardTimeLog(), 'job_card_id');
    }
}
