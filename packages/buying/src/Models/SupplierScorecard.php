<?php

namespace JeffersonGoncalves\Erp\Buying\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Buying\Support\ModelResolver;

/**
 * A periodic performance scorecard for a supplier. The weighted score is
 * derived from its criteria and mapped to a standing (grade).
 *
 * @property int $id
 * @property int $supplier_id
 * @property string $name
 * @property string|null $weighting_function
 * @property string|null $standing
 * @property float $score
 * @property bool $disabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Supplier|null $supplier
 * @property-read Collection<int, SupplierScorecardCriteria> $criteria
 */
class SupplierScorecard extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'name',
        'weighting_function',
        'standing',
        'score',
        'disabled',
    ];

    protected $attributes = [
        'score' => 0,
        'disabled' => false,
    ];

    protected $casts = [
        'score' => 'float',
        'disabled' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-buying.table_prefix') ?? '').'supplier_scorecards';
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::supplier(), 'supplier_id');
    }

    public function criteria(): HasMany
    {
        return $this->hasMany(SupplierScorecardCriteria::class, 'supplier_scorecard_id');
    }

    /** @param  Builder<static>  $query */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('disabled', false);
    }
}
