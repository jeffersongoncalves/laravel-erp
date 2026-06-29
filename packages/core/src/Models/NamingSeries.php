<?php

namespace JeffersonGoncalves\Erp\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Models\Contracts\NamingSeriesContract;

/**
 * @property int $id
 * @property string $series
 * @property int $current
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class NamingSeries extends Model implements NamingSeriesContract
{
    protected $fillable = [
        'series',
        'current',
    ];

    protected $casts = [
        'current' => 'integer',
    ];

    public function getTable(): string
    {
        return (config('erp-core.table_prefix') ?? '').'naming_series';
    }
}
