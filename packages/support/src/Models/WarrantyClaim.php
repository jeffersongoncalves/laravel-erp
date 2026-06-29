<?php

namespace JeffersonGoncalves\Erp\Support\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Support\Enums\WarrantyClaimStatus;

/**
 * @property int $id
 * @property string $party_type
 * @property int|null $party_id
 * @property string|null $customer_name
 * @property string|null $serial_no
 * @property string|null $item_code
 * @property string|null $complaint
 * @property WarrantyClaimStatus $status
 * @property Carbon|null $complaint_date
 * @property Carbon|null $resolution_date
 * @property string|null $resolution_details
 * @property int|null $company_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class WarrantyClaim extends Model
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'party_type',
        'party_id',
        'customer_name',
        'serial_no',
        'item_code',
        'complaint',
        'status',
        'complaint_date',
        'resolution_date',
        'resolution_details',
        'company_id',
    ];

    protected $attributes = [
        'party_type' => 'Customer',
        'status' => 'Open',
    ];

    protected $casts = [
        'status' => WarrantyClaimStatus::class,
        'complaint_date' => 'date',
        'resolution_date' => 'date',
    ];

    public function getTable(): string
    {
        return (config('erp-support.table_prefix') ?? '').'warranty_claims';
    }
}
