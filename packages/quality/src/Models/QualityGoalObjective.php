<?php

namespace JeffersonGoncalves\Erp\Quality\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Quality\Support\ModelResolver;

/**
 * @property int $id
 * @property int $quality_goal_id
 * @property string $objective
 * @property string|null $target
 * @property string|null $uom
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read QualityGoal|null $qualityGoal
 */
class QualityGoalObjective extends Model
{
    use HasFactory;

    protected $fillable = [
        'quality_goal_id',
        'objective',
        'target',
        'uom',
    ];

    public function getTable(): string
    {
        return (config('erp-quality.table_prefix') ?? '').'quality_goal_objectives';
    }

    public function qualityGoal(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::qualityGoal(), 'quality_goal_id');
    }
}
