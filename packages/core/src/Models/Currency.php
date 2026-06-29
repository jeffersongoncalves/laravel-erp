<?php

namespace JeffersonGoncalves\Erp\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Models\Contracts\CurrencyContract;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $symbol
 * @property string|null $fraction
 * @property int|null $fraction_units
 * @property string|null $number_format
 * @property bool $enabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Currency extends Model implements CurrencyContract
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'fraction',
        'fraction_units',
        'number_format',
        'enabled',
    ];

    protected $casts = [
        'fraction_units' => 'integer',
        'enabled' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-core.table_prefix') ?? '').'currencies';
    }

    /** @param  Builder<static>  $query */
    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }
}
