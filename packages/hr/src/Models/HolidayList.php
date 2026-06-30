<?php

namespace JeffersonGoncalves\Erp\Hr\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Hr\Support\ModelResolver;

/**
 * @property int $id
 * @property string $name
 * @property int|null $company_id
 * @property Carbon|null $from_date
 * @property Carbon|null $to_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company|null $company
 * @property-read Collection<int, Holiday> $holidays
 */
class HolidayList extends Model
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'name',
        'company_id',
        'from_date',
        'to_date',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
    ];

    public function getTable(): string
    {
        return (config('erp-hr.table_prefix') ?? '').'holiday_lists';
    }

    public function holidays(): HasMany
    {
        return $this->hasMany(ModelResolver::holiday(), 'holiday_list_id');
    }
}
