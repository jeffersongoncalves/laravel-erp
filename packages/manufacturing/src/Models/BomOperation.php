<?php

namespace JeffersonGoncalves\Erp\Manufacturing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Manufacturing\Support\ModelResolver;

/**
 * @property int $id
 * @property int $bom_id
 * @property int|null $operation_id
 * @property int|null $workstation_id
 * @property float $time_in_mins
 * @property float $hour_rate
 * @property float $operating_cost
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Bom|null $bom
 * @property-read Operation|null $operation
 * @property-read Workstation|null $workstation
 */
class BomOperation extends Model
{
    use HasFactory;

    protected $fillable = [
        'bom_id',
        'operation_id',
        'workstation_id',
        'time_in_mins',
        'hour_rate',
        'operating_cost',
    ];

    protected $attributes = [
        'time_in_mins' => 0,
        'hour_rate' => 0,
        'operating_cost' => 0,
    ];

    protected $casts = [
        'time_in_mins' => 'float',
        'hour_rate' => 'float',
        'operating_cost' => 'float',
    ];

    protected static function booted(): void
    {
        static::saving(function (BomOperation $operation): void {
            $operation->operating_cost = (float) $operation->time_in_mins / 60 * (float) $operation->hour_rate;
        });

        static::saved(fn (BomOperation $operation) => $operation->recalculateBom());
        static::deleted(fn (BomOperation $operation) => $operation->recalculateBom());
    }

    public function getTable(): string
    {
        return (config('erp-manufacturing.table_prefix') ?? '').'bom_operations';
    }

    public function bom(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::bom(), 'bom_id');
    }

    public function operation(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::operation(), 'operation_id');
    }

    public function workstation(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::workstation(), 'workstation_id');
    }

    protected function recalculateBom(): void
    {
        $this->bom?->recalculateCosts();
    }
}
