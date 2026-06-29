<?php

namespace JeffersonGoncalves\Erp\Quality\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Quality\Support\ModelResolver;

/**
 * @property int $id
 * @property string $quality_procedure_name
 * @property int|null $parent_quality_procedure_id
 * @property bool $is_group
 * @property string|null $process_owner
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read QualityProcedure|null $parent
 * @property-read Collection<int, QualityProcedure> $children
 * @property-read Collection<int, QualityProcedureProcess> $processes
 */
class QualityProcedure extends Model
{
    use HasFactory;

    protected $fillable = [
        'quality_procedure_name',
        'parent_quality_procedure_id',
        'is_group',
        'process_owner',
    ];

    protected $attributes = [
        'is_group' => false,
    ];

    protected $casts = [
        'is_group' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-quality.table_prefix') ?? '').'quality_procedures';
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::qualityProcedure(), 'parent_quality_procedure_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ModelResolver::qualityProcedure(), 'parent_quality_procedure_id');
    }

    public function processes(): HasMany
    {
        return $this->hasMany(ModelResolver::qualityProcedureProcess(), 'quality_procedure_id');
    }
}
