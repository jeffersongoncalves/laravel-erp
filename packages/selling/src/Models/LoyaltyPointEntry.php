<?php

namespace JeffersonGoncalves\Erp\Selling\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $loyalty_program_id
 * @property string $party_type
 * @property int|null $party_id
 * @property Carbon $posting_date
 * @property float $purchase_amount
 * @property int $loyalty_points
 * @property Carbon|null $expiry_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read LoyaltyProgram|null $loyaltyProgram
 */
class LoyaltyPointEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'loyalty_program_id',
        'party_type',
        'party_id',
        'posting_date',
        'purchase_amount',
        'loyalty_points',
        'expiry_date',
    ];

    protected $attributes = [
        'party_type' => 'Customer',
        'purchase_amount' => 0,
        'loyalty_points' => 0,
    ];

    protected $casts = [
        'posting_date' => 'date',
        'purchase_amount' => 'float',
        'loyalty_points' => 'integer',
        'expiry_date' => 'date',
    ];

    public function getTable(): string
    {
        return (config('erp-selling.table_prefix') ?? '').'loyalty_point_entries';
    }

    public function loyaltyProgram(): BelongsTo
    {
        return $this->belongsTo(LoyaltyProgram::class, 'loyalty_program_id');
    }
}
