<?php

namespace JeffersonGoncalves\Erp\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Models\Contracts\UomContract;

/**
 * @property int $id
 * @property string $name
 * @property bool $enabled
 * @property bool $must_be_whole_number
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Uom extends Model implements UomContract
{
    use HasFactory;

    protected $fillable = [
        'name',
        'enabled',
        'must_be_whole_number',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'must_be_whole_number' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-core.table_prefix') ?? '').'uoms';
    }

    /** @param  Builder<static>  $query */
    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }
}
