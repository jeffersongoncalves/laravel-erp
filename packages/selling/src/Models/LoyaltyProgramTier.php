<?php

namespace JeffersonGoncalves\Erp\Selling\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $loyalty_program_id
 * @property string $tier_name
 * @property float $min_spent
 * @property float $collection_factor
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read LoyaltyProgram|null $loyaltyProgram
 */
class LoyaltyProgramTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'loyalty_program_id',
        'tier_name',
        'min_spent',
        'collection_factor',
    ];

    protected $attributes = [
        'min_spent' => 0,
        'collection_factor' => 1,
    ];

    protected $casts = [
        'min_spent' => 'float',
        'collection_factor' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-selling.table_prefix') ?? '').'loyalty_program_tiers';
    }

    public function loyaltyProgram(): BelongsTo
    {
        return $this->belongsTo(LoyaltyProgram::class, 'loyalty_program_id');
    }
}
