<?php

namespace JeffersonGoncalves\Erp\Support\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Support\Enums\IssuePriority;
use JeffersonGoncalves\Erp\Support\Support\ModelResolver;

/**
 * @property int $id
 * @property int $service_level_agreement_id
 * @property IssuePriority $priority
 * @property int $response_time
 * @property int $resolution_time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ServiceLevelAgreement|null $serviceLevelAgreement
 */
class ServiceLevelPriority extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_level_agreement_id',
        'priority',
        'response_time',
        'resolution_time',
    ];

    protected $attributes = [
        'priority' => 'Medium',
        'response_time' => 0,
        'resolution_time' => 0,
    ];

    protected $casts = [
        'priority' => IssuePriority::class,
        'response_time' => 'integer',
        'resolution_time' => 'integer',
    ];

    public function getTable(): string
    {
        return (config('erp-support.table_prefix') ?? '').'service_level_priorities';
    }

    public function serviceLevelAgreement(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::serviceLevelAgreement(), 'service_level_agreement_id');
    }
}
