<?php

namespace JeffersonGoncalves\Erp\Selling\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;

/**
 * @property int $id
 * @property string $loyalty_program_name
 * @property int|null $company_id
 * @property Carbon|null $from_date
 * @property Carbon|null $to_date
 * @property float $conversion_factor
 * @property int $expiry_duration
 * @property bool $disabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, LoyaltyProgramTier> $tiers
 * @property-read Collection<int, LoyaltyPointEntry> $pointEntries
 */
class LoyaltyProgram extends Model
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'loyalty_program_name',
        'company_id',
        'from_date',
        'to_date',
        'conversion_factor',
        'expiry_duration',
        'disabled',
    ];

    protected $attributes = [
        'conversion_factor' => 1,
        'expiry_duration' => 0,
        'disabled' => false,
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'conversion_factor' => 'float',
        'expiry_duration' => 'integer',
        'disabled' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-selling.table_prefix') ?? '').'loyalty_programs';
    }

    public function tiers(): HasMany
    {
        return $this->hasMany(LoyaltyProgramTier::class, 'loyalty_program_id');
    }

    public function pointEntries(): HasMany
    {
        return $this->hasMany(LoyaltyPointEntry::class, 'loyalty_program_id');
    }

    /** @param  Builder<static>  $query */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('disabled', false);
    }
}
