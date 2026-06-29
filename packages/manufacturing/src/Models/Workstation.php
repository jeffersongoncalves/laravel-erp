<?php

namespace JeffersonGoncalves\Erp\Manufacturing\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Manufacturing\Support\ModelResolver;

/**
 * @property int $id
 * @property string $name
 * @property float $hour_rate
 * @property int $production_capacity
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Operation> $operations
 */
class Workstation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'hour_rate',
        'production_capacity',
        'description',
    ];

    protected $attributes = [
        'hour_rate' => 0,
        'production_capacity' => 1,
    ];

    protected $casts = [
        'hour_rate' => 'float',
        'production_capacity' => 'integer',
    ];

    public function getTable(): string
    {
        return (config('erp-manufacturing.table_prefix') ?? '').'workstations';
    }

    public function operations(): HasMany
    {
        return $this->hasMany(ModelResolver::operation(), 'workstation_id');
    }
}
