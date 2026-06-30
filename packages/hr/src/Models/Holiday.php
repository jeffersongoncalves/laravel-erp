<?php

namespace JeffersonGoncalves\Erp\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Hr\Support\ModelResolver;

/**
 * @property int $id
 * @property int $holiday_list_id
 * @property Carbon|null $holiday_date
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read HolidayList|null $holidayList
 */
class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'holiday_list_id',
        'holiday_date',
        'description',
    ];

    protected $casts = [
        'holiday_date' => 'date',
    ];

    public function getTable(): string
    {
        return (config('erp-hr.table_prefix') ?? '').'holidays';
    }

    public function holidayList(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::holidayList(), 'holiday_list_id');
    }
}
