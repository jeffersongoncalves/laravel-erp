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
 * @property bool $disabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, RoutingOperation> $operations
 */
class Routing extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'disabled',
    ];

    protected $attributes = [
        'disabled' => false,
    ];

    protected $casts = [
        'disabled' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-manufacturing.table_prefix') ?? '').'routings';
    }

    public function operations(): HasMany
    {
        return $this->hasMany(ModelResolver::routingOperation(), 'routing_id');
    }
}
