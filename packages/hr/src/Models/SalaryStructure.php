<?php

namespace JeffersonGoncalves\Erp\Hr\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Hr\Models\Contracts\SalaryStructureContract;
use JeffersonGoncalves\Erp\Hr\Support\ModelResolver;

/**
 * @property int $id
 * @property string $name
 * @property int|null $company_id
 * @property bool $is_active
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company|null $company
 * @property-read Collection<int, SalaryStructureComponent> $components
 * @property-read Collection<int, SalaryStructureAssignment> $assignments
 */
class SalaryStructure extends Model implements SalaryStructureContract, SubmittableDocument
{
    use HasCompany;
    use HasFactory;
    use IsSubmittable;

    protected $fillable = [
        'name',
        'company_id',
        'is_active',
        'docstatus',
    ];

    protected $attributes = [
        'is_active' => true,
        'docstatus' => 0,
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'docstatus' => DocStatus::class,
    ];

    public function getTable(): string
    {
        return (config('erp-hr.table_prefix') ?? '').'salary_structures';
    }

    public function components(): HasMany
    {
        return $this->hasMany(ModelResolver::salaryStructureComponent(), 'salary_structure_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ModelResolver::salaryStructureAssignment(), 'salary_structure_id');
    }
}
