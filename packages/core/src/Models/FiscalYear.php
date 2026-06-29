<?php

namespace JeffersonGoncalves\Erp\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Models\Contracts\FiscalYearContract;

/**
 * @property int $id
 * @property string $name
 * @property Carbon $year_start_date
 * @property Carbon $year_end_date
 * @property bool $is_short_year
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class FiscalYear extends Model implements FiscalYearContract
{
    use HasFactory;

    protected $fillable = [
        'name',
        'year_start_date',
        'year_end_date',
        'is_short_year',
    ];

    protected $casts = [
        'year_start_date' => 'date',
        'year_end_date' => 'date',
        'is_short_year' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-core.table_prefix') ?? '').'fiscal_years';
    }
}
