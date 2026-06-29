<?php

namespace JeffersonGoncalves\Erp\Crm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;

/**
 * @property int $id
 * @property string $party_type
 * @property int|null $party_id
 * @property string $party_name
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 * @property string $status
 * @property bool $is_signed
 * @property string|null $contract_terms
 * @property int|null $company_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Contract extends Model
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'party_type',
        'party_id',
        'party_name',
        'start_date',
        'end_date',
        'status',
        'is_signed',
        'contract_terms',
        'company_id',
    ];

    protected $attributes = [
        'party_type' => 'Customer',
        'status' => 'Unsigned',
        'is_signed' => false,
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_signed' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-crm.table_prefix') ?? '').'contracts';
    }
}
