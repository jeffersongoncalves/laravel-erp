<?php

namespace JeffersonGoncalves\Erp\Quality\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Quality\Support\ModelResolver;

/**
 * @property int $id
 * @property int $quality_action_id
 * @property string $problem
 * @property string|null $resolution
 * @property string $status
 * @property string|null $responsible
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read QualityAction|null $qualityAction
 */
class QualityActionResolution extends Model
{
    use HasFactory;

    protected $fillable = [
        'quality_action_id',
        'problem',
        'resolution',
        'status',
        'responsible',
    ];

    protected $attributes = [
        'status' => 'Open',
    ];

    public function getTable(): string
    {
        return (config('erp-quality.table_prefix') ?? '').'quality_action_resolutions';
    }

    public function qualityAction(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::qualityAction(), 'quality_action_id');
    }
}
