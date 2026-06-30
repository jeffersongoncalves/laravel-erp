<?php

namespace JeffersonGoncalves\Erp\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Hr\Enums\SalaryComponentType;
use JeffersonGoncalves\Erp\Hr\Support\ModelResolver;

/**
 * @property int $id
 * @property int $salary_slip_id
 * @property int $salary_component_id
 * @property SalaryComponentType $type
 * @property float $amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read SalarySlip|null $salarySlip
 * @property-read SalaryComponent|null $salaryComponent
 */
class SalarySlipComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'salary_slip_id',
        'salary_component_id',
        'type',
        'amount',
    ];

    protected $attributes = [
        'type' => 'Earning',
        'amount' => 0,
    ];

    protected $casts = [
        'type' => SalaryComponentType::class,
        'amount' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-hr.table_prefix') ?? '').'salary_slip_components';
    }

    public function salarySlip(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::salarySlip(), 'salary_slip_id');
    }

    public function salaryComponent(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::salaryComponent(), 'salary_component_id');
    }
}
