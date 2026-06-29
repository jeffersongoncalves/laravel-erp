<?php

namespace JeffersonGoncalves\Erp\Manufacturing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Manufacturing\Support\ModelResolver;

/**
 * @property int $id
 * @property int $job_card_id
 * @property Carbon|null $from_time
 * @property Carbon|null $to_time
 * @property float $time_in_mins
 * @property float $completed_qty
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read JobCard|null $jobCard
 */
class JobCardTimeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_card_id',
        'from_time',
        'to_time',
        'time_in_mins',
        'completed_qty',
    ];

    protected $attributes = [
        'time_in_mins' => 0,
        'completed_qty' => 0,
    ];

    protected $casts = [
        'from_time' => 'datetime',
        'to_time' => 'datetime',
        'time_in_mins' => 'float',
        'completed_qty' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-manufacturing.table_prefix') ?? '').'job_card_time_logs';
    }

    public function jobCard(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::jobCard(), 'job_card_id');
    }
}
