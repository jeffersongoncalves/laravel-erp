<?php

namespace JeffersonGoncalves\Erp\Selling\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property float $commission_rate
 * @property string|null $partner_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class SalesPartner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'commission_rate',
        'partner_type',
    ];

    protected $attributes = [
        'commission_rate' => 0,
    ];

    protected $casts = [
        'commission_rate' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-selling.table_prefix') ?? '').'sales_partners';
    }
}
