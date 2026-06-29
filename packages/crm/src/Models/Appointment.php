<?php

namespace JeffersonGoncalves\Erp\Crm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;

/**
 * @property int $id
 * @property Carbon $scheduled_time
 * @property string $status
 * @property string|null $customer_name
 * @property string|null $customer_email
 * @property string|null $customer_phone
 * @property string|null $party_type
 * @property int|null $party_id
 * @property int|null $company_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Appointment extends Model
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'scheduled_time',
        'status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'party_type',
        'party_id',
        'company_id',
    ];

    protected $attributes = [
        'status' => 'Open',
    ];

    protected $casts = [
        'scheduled_time' => 'datetime',
    ];

    public function getTable(): string
    {
        return (config('erp-crm.table_prefix') ?? '').'appointments';
    }
}
