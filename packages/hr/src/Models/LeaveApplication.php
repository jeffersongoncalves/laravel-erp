<?php

namespace JeffersonGoncalves\Erp\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Hr\Enums\LeaveApplicationStatus;
use JeffersonGoncalves\Erp\Hr\Services\LeaveService;
use JeffersonGoncalves\Erp\Hr\Support\ModelResolver;

/**
 * @property int $id
 * @property int $employee_id
 * @property int $leave_type_id
 * @property Carbon|null $from_date
 * @property Carbon|null $to_date
 * @property float $total_leave_days
 * @property LeaveApplicationStatus $status
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Employee|null $employee
 * @property-read LeaveType|null $leaveType
 */
class LeaveApplication extends Model implements SubmittableDocument
{
    use HasFactory;
    use IsSubmittable {
        submit as protected performSubmit;
    }

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'from_date',
        'to_date',
        'total_leave_days',
        'status',
        'docstatus',
    ];

    protected $attributes = [
        'total_leave_days' => 0,
        'status' => 'Open',
        'docstatus' => 0,
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'total_leave_days' => 'float',
        'status' => LeaveApplicationStatus::class,
        'docstatus' => DocStatus::class,
    ];

    public function getTable(): string
    {
        return (config('erp-hr.table_prefix') ?? '').'leave_applications';
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::employee(), 'employee_id');
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::leaveType(), 'leave_type_id');
    }

    /**
     * Submitting a leave application first validates the requested days against
     * the leave type's allowance (unless the type permits a negative balance),
     * then runs the standard submit transition.
     */
    public function submit(): void
    {
        app(LeaveService::class)->guardBalance($this);

        $this->performSubmit();
    }
}
