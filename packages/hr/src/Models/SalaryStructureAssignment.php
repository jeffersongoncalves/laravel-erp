<?php

namespace JeffersonGoncalves\Erp\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Hr\Support\ModelResolver;

/**
 * @property int $id
 * @property int $employee_id
 * @property int $salary_structure_id
 * @property Carbon|null $from_date
 * @property float $base
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Employee|null $employee
 * @property-read SalaryStructure|null $salaryStructure
 */
class SalaryStructureAssignment extends Model implements SubmittableDocument
{
    use HasFactory;
    use IsSubmittable;

    protected $fillable = [
        'employee_id',
        'salary_structure_id',
        'from_date',
        'base',
        'docstatus',
    ];

    protected $attributes = [
        'base' => 0,
        'docstatus' => 0,
    ];

    protected $casts = [
        'from_date' => 'date',
        'base' => 'float',
        'docstatus' => DocStatus::class,
    ];

    public function getTable(): string
    {
        return (config('erp-hr.table_prefix') ?? '').'salary_structure_assignments';
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::employee(), 'employee_id');
    }

    public function salaryStructure(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::salaryStructure(), 'salary_structure_id');
    }
}
