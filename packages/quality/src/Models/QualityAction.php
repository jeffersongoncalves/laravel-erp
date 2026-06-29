<?php

namespace JeffersonGoncalves\Erp\Quality\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Quality\Enums\QualityActionStatus;
use JeffersonGoncalves\Erp\Quality\Support\ModelResolver;

/**
 * @property int $id
 * @property string $corrective_preventive
 * @property QualityActionStatus $status
 * @property string|null $review
 * @property Carbon|null $date
 * @property string|null $goal
 * @property string|null $procedure
 * @property int|null $company_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, QualityActionResolution> $resolutions
 */
class QualityAction extends Model
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'corrective_preventive',
        'status',
        'review',
        'date',
        'goal',
        'procedure',
        'company_id',
    ];

    protected $attributes = [
        'corrective_preventive' => 'Corrective',
        'status' => 'Open',
    ];

    protected $casts = [
        'status' => QualityActionStatus::class,
        'date' => 'date',
    ];

    public function getTable(): string
    {
        return (config('erp-quality.table_prefix') ?? '').'quality_actions';
    }

    public function resolutions(): HasMany
    {
        return $this->hasMany(ModelResolver::qualityActionResolution(), 'quality_action_id');
    }
}
