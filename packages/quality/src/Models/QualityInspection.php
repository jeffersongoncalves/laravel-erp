<?php

namespace JeffersonGoncalves\Erp\Quality\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Quality\Enums\InspectionResult;
use JeffersonGoncalves\Erp\Quality\Enums\InspectionType;
use JeffersonGoncalves\Erp\Quality\Enums\ReadingStatus;
use JeffersonGoncalves\Erp\Quality\Support\ModelResolver;

/**
 * @property int $id
 * @property string|null $item_code
 * @property string|null $item_name
 * @property InspectionType $inspection_type
 * @property string|null $reference_type
 * @property string|null $reference_name
 * @property string $sample_size
 * @property int|null $quality_inspection_template_id
 * @property InspectionResult $status
 * @property string|null $inspected_by
 * @property Carbon|null $report_date
 * @property int|null $company_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read QualityInspectionTemplate|null $qualityInspectionTemplate
 * @property-read Collection<int, QualityInspectionReading> $readings
 */
class QualityInspection extends Model
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'item_code',
        'item_name',
        'inspection_type',
        'reference_type',
        'reference_name',
        'sample_size',
        'quality_inspection_template_id',
        'status',
        'inspected_by',
        'report_date',
        'company_id',
    ];

    protected $attributes = [
        'inspection_type' => 'Incoming',
        'sample_size' => 1,
        'status' => 'Accepted',
    ];

    protected $casts = [
        'inspection_type' => InspectionType::class,
        'status' => InspectionResult::class,
        'sample_size' => 'decimal:9',
        'report_date' => 'date',
    ];

    public function getTable(): string
    {
        return (config('erp-quality.table_prefix') ?? '').'quality_inspections';
    }

    public function qualityInspectionTemplate(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::qualityInspectionTemplate(), 'quality_inspection_template_id');
    }

    public function readings(): HasMany
    {
        return $this->hasMany(ModelResolver::qualityInspectionReading(), 'quality_inspection_id');
    }

    /**
     * Recompute the overall inspection status from its readings: the inspection
     * is Rejected when any reading is Rejected, otherwise Accepted.
     */
    public function recomputeStatus(): void
    {
        $hasRejected = $this->readings()
            ->where('status', ReadingStatus::Rejected->value)
            ->exists();

        $status = $hasRejected ? InspectionResult::Rejected : InspectionResult::Accepted;

        if ($this->status !== $status) {
            $this->status = $status;
            $this->saveQuietly();
        }
    }
}
