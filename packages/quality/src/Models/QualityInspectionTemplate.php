<?php

namespace JeffersonGoncalves\Erp\Quality\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Quality\Support\ModelResolver;

/**
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, QualityInspectionTemplateParameter> $parameters
 */
class QualityInspectionTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function getTable(): string
    {
        return (config('erp-quality.table_prefix') ?? '').'quality_inspection_templates';
    }

    public function parameters(): HasMany
    {
        return $this->hasMany(ModelResolver::qualityInspectionTemplateParameter(), 'quality_inspection_template_id');
    }
}
