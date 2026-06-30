<?php

namespace JeffersonGoncalves\Erp\Hr\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Core\Models\Department;
use JeffersonGoncalves\Erp\Core\Models\Designation;
use JeffersonGoncalves\Erp\Core\Support\ModelResolver as CoreModelResolver;
use JeffersonGoncalves\Erp\Hr\Enums\EmployeeStatus;
use JeffersonGoncalves\Erp\Hr\Enums\Gender;
use JeffersonGoncalves\Erp\Hr\Models\Contracts\EmployeeContract;
use JeffersonGoncalves\Erp\Hr\Support\ModelResolver;

/**
 * @property int $id
 * @property string $employee_number
 * @property int|null $company_id
 * @property int|null $department_id
 * @property int|null $designation_id
 * @property string $first_name
 * @property string|null $last_name
 * @property Gender|null $gender
 * @property Carbon|null $date_of_birth
 * @property Carbon|null $date_of_joining
 * @property EmployeeStatus $status
 * @property Carbon|null $date_of_leaving
 * @property float $ctc
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company|null $company
 * @property-read Department|null $department
 * @property-read Designation|null $designation
 * @property-read Collection<int, SalaryStructureAssignment> $salaryStructureAssignments
 */
class Employee extends Model implements EmployeeContract
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'employee_number',
        'company_id',
        'department_id',
        'designation_id',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'date_of_joining',
        'status',
        'date_of_leaving',
        'ctc',
    ];

    protected $attributes = [
        'status' => 'Active',
        'ctc' => 0,
    ];

    protected $casts = [
        'gender' => Gender::class,
        'date_of_birth' => 'date',
        'date_of_joining' => 'date',
        'status' => EmployeeStatus::class,
        'date_of_leaving' => 'date',
        'ctc' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-hr.table_prefix') ?? '').'employees';
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(CoreModelResolver::department(), 'department_id');
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(CoreModelResolver::designation(), 'designation_id');
    }

    public function salaryStructureAssignments(): HasMany
    {
        return $this->hasMany(ModelResolver::salaryStructureAssignment(), 'employee_id');
    }

    /** @param  Builder<static>  $query */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', EmployeeStatus::Active);
    }
}
