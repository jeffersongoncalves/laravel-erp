<?php

namespace JeffersonGoncalves\Erp\Quality\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Quality\Support\ModelResolver;

/**
 * @property int $id
 * @property int $quality_review_id
 * @property string $objective
 * @property string|null $target
 * @property string|null $achieved
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read QualityReview|null $qualityReview
 */
class QualityReviewObjective extends Model
{
    use HasFactory;

    protected $fillable = [
        'quality_review_id',
        'objective',
        'target',
        'achieved',
        'status',
    ];

    protected $attributes = [
        'status' => 'Open',
    ];

    public function getTable(): string
    {
        return (config('erp-quality.table_prefix') ?? '').'quality_review_objectives';
    }

    public function qualityReview(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::qualityReview(), 'quality_review_id');
    }
}
