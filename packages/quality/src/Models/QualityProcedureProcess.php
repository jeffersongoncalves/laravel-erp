<?php

namespace JeffersonGoncalves\Erp\Quality\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Quality\Support\ModelResolver;

/**
 * @property int $id
 * @property int $quality_procedure_id
 * @property string $process_description
 * @property string|null $role
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read QualityProcedure|null $qualityProcedure
 */
class QualityProcedureProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'quality_procedure_id',
        'process_description',
        'role',
    ];

    public function getTable(): string
    {
        return (config('erp-quality.table_prefix') ?? '').'quality_procedure_processes';
    }

    public function qualityProcedure(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::qualityProcedure(), 'quality_procedure_id');
    }
}
