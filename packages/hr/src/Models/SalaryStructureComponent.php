<?php

namespace JeffersonGoncalves\Erp\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Hr\Support\ModelResolver;

/**
 * @property int $id
 * @property int $salary_structure_id
 * @property int $salary_component_id
 * @property float $amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read SalaryStructure|null $salaryStructure
 * @property-read SalaryComponent|null $salaryComponent
 */
class SalaryStructureComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'salary_structure_id',
        'salary_component_id',
        'amount',
    ];

    protected $attributes = [
        'amount' => 0,
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-hr.table_prefix') ?? '').'salary_structure_components';
    }

    public function salaryStructure(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::salaryStructure(), 'salary_structure_id');
    }

    public function salaryComponent(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::salaryComponent(), 'salary_component_id');
    }
}
