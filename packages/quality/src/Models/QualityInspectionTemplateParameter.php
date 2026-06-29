<?php

namespace JeffersonGoncalves\Erp\Quality\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Quality\Support\ModelResolver;

/**
 * @property int $id
 * @property int $quality_inspection_template_id
 * @property string $specification
 * @property string|null $value
 * @property bool $numeric
 * @property string|null $min_value
 * @property string|null $max_value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read QualityInspectionTemplate|null $qualityInspectionTemplate
 */
class QualityInspectionTemplateParameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'quality_inspection_template_id',
        'specification',
        'value',
        'numeric',
        'min_value',
        'max_value',
    ];

    protected $attributes = [
        'numeric' => false,
    ];

    protected $casts = [
        'numeric' => 'boolean',
        'min_value' => 'decimal:9',
        'max_value' => 'decimal:9',
    ];

    public function getTable(): string
    {
        return (config('erp-quality.table_prefix') ?? '').'quality_inspection_template_parameters';
    }

    public function qualityInspectionTemplate(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::qualityInspectionTemplate(), 'quality_inspection_template_id');
    }
}
