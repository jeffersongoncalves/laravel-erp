<?php

namespace JeffersonGoncalves\Erp\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Hr\Enums\AttendanceStatus;
use JeffersonGoncalves\Erp\Hr\Support\ModelResolver;

/**
 * @property int $id
 * @property int $employee_id
 * @property int|null $company_id
 * @property Carbon|null $attendance_date
 * @property AttendanceStatus $status
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Employee|null $employee
 * @property-read Company|null $company
 */
class Attendance extends Model implements SubmittableDocument
{
    use HasCompany;
    use HasFactory;
    use IsSubmittable;

    protected $fillable = [
        'employee_id',
        'company_id',
        'attendance_date',
        'status',
        'docstatus',
    ];

    protected $attributes = [
        'status' => 'Present',
        'docstatus' => 0,
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'status' => AttendanceStatus::class,
        'docstatus' => DocStatus::class,
    ];

    public function getTable(): string
    {
        return (config('erp-hr.table_prefix') ?? '').'attendances';
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::employee(), 'employee_id');
    }
}
