<?php

namespace JeffersonGoncalves\Erp\Manufacturing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Manufacturing\Support\ModelResolver;

/**
 * @property int $id
 * @property int $routing_id
 * @property int|null $operation_id
 * @property int|null $workstation_id
 * @property int $sequence_id
 * @property float $time_in_mins
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Routing|null $routing
 * @property-read Operation|null $operation
 * @property-read Workstation|null $workstation
 */
class RoutingOperation extends Model
{
    use HasFactory;

    protected $fillable = [
        'routing_id',
        'operation_id',
        'workstation_id',
        'sequence_id',
        'time_in_mins',
    ];

    protected $attributes = [
        'sequence_id' => 0,
        'time_in_mins' => 0,
    ];

    protected $casts = [
        'sequence_id' => 'integer',
        'time_in_mins' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-manufacturing.table_prefix') ?? '').'routing_operations';
    }

    public function routing(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::routing(), 'routing_id');
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::operation(), 'operation_id');
    }

    public function workstation(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::workstation(), 'workstation_id');
    }
}
