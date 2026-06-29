<?php

namespace JeffersonGoncalves\Erp\Quality\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Quality\Support\ModelResolver;

/**
 * @property int $id
 * @property string|null $goal
 * @property int|null $quality_goal_id
 * @property Carbon|null $date
 * @property string $status
 * @property int|null $company_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read QualityGoal|null $qualityGoal
 * @property-read Collection<int, QualityReviewObjective> $objectives
 */
class QualityReview extends Model
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'goal',
        'quality_goal_id',
        'date',
        'status',
        'company_id',
    ];

    protected $attributes = [
        'status' => 'Open',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function getTable(): string
    {
        return (config('erp-quality.table_prefix') ?? '').'quality_reviews';
    }

    public function qualityGoal(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::qualityGoal(), 'quality_goal_id');
    }

    public function objectives(): HasMany
    {
        return $this->hasMany(ModelResolver::qualityReviewObjective(), 'quality_review_id');
    }
}
