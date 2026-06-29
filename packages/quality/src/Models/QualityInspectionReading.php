<?php

namespace JeffersonGoncalves\Erp\Quality\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Quality\Enums\ReadingStatus;
use JeffersonGoncalves\Erp\Quality\Support\ModelResolver;

/**
 * @property int $id
 * @property int $quality_inspection_id
 * @property string $specification
 * @property string|null $value
 * @property string|null $reading_value
 * @property ReadingStatus $status
 * @property string|null $min_value
 * @property string|null $max_value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read QualityInspection|null $qualityInspection
 */
class QualityInspectionReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'quality_inspection_id',
        'specification',
        'value',
        'reading_value',
        'status',
        'min_value',
        'max_value',
    ];

    protected $attributes = [
        'status' => 'Accepted',
    ];

    protected $casts = [
        'status' => ReadingStatus::class,
        'min_value' => 'decimal:9',
        'max_value' => 'decimal:9',
    ];

    protected static function booted(): void
    {
        static::saved(function (QualityInspectionReading $reading): void {
            $reading->qualityInspection?->recomputeStatus();
        });

        static::deleted(function (QualityInspectionReading $reading): void {
            $reading->qualityInspection?->recomputeStatus();
        });
    }

    public function getTable(): string
    {
        return (config('erp-quality.table_prefix') ?? '').'quality_inspection_readings';
    }

    public function qualityInspection(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::qualityInspection(), 'quality_inspection_id');
    }
}
