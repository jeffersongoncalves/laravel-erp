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
 * @property string $goal
 * @property string|null $procedure
 * @property string $frequency
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, QualityGoalObjective> $objectives
 */
class QualityGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'goal',
        'procedure',
        'frequency',
    ];

    protected $attributes = [
        'frequency' => 'None',
    ];

    public function getTable(): string
    {
        return (config('erp-quality.table_prefix') ?? '').'quality_goals';
    }

    public function objectives(): HasMany
    {
        return $this->hasMany(ModelResolver::qualityGoalObjective(), 'quality_goal_id');
    }
}
