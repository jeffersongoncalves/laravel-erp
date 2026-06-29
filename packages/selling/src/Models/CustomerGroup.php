<?php

namespace JeffersonGoncalves\Erp\Selling\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Selling\Models\Contracts\CustomerGroupContract;
use JeffersonGoncalves\Erp\Selling\Support\ModelResolver;

/**
 * @property int $id
 * @property string $name
 * @property int|null $parent_customer_group_id
 * @property bool $is_group
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read CustomerGroup|null $parent
 * @property-read Collection<int, CustomerGroup> $children
 * @property-read Collection<int, Customer> $customers
 */
class CustomerGroup extends Model implements CustomerGroupContract
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_customer_group_id',
        'is_group',
    ];

    protected $attributes = [
        'is_group' => false,
    ];

    protected $casts = [
        'is_group' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-selling.table_prefix') ?? '').'customer_groups';
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::customerGroup(), 'parent_customer_group_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ModelResolver::customerGroup(), 'parent_customer_group_id');
    }

    public function customers(): HasMany
    {
        return $this->hasMany(ModelResolver::customer(), 'customer_group_id');
    }

    /** @param  Builder<static>  $query */
    public function scopeGroups(Builder $query): Builder
    {
        return $query->where('is_group', true);
    }
}
