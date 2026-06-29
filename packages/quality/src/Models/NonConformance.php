<?php

namespace JeffersonGoncalves\Erp\Quality\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Quality\Enums\NonConformanceStatus;
use JeffersonGoncalves\Erp\Quality\Support\ModelResolver;

/**
 * @property int $id
 * @property string $subject
 * @property string|null $procedure
 * @property int|null $quality_procedure_id
 * @property NonConformanceStatus $status
 * @property string|null $details
 * @property string|null $corrective_action
 * @property string|null $preventive_action
 * @property int|null $company_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read QualityProcedure|null $qualityProcedure
 */
class NonConformance extends Model
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'subject',
        'procedure',
        'quality_procedure_id',
        'status',
        'details',
        'corrective_action',
        'preventive_action',
        'company_id',
    ];

    protected $attributes = [
        'status' => 'Open',
    ];

    protected $casts = [
        'status' => NonConformanceStatus::class,
    ];

    public function getTable(): string
    {
        return (config('erp-quality.table_prefix') ?? '').'non_conformances';
    }

    public function qualityProcedure(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::qualityProcedure(), 'quality_procedure_id');
    }
}
