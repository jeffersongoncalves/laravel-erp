<?php

namespace JeffersonGoncalves\Erp\Buying\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Buying\Models\Contracts\SupplierGroupContract;
use JeffersonGoncalves\Erp\Buying\Support\ModelResolver;

/**
 * @property int $id
 * @property string $name
 * @property int|null $parent_supplier_group_id
 * @property bool $is_group
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read SupplierGroup|null $parent
 * @property-read Collection<int, SupplierGroup> $children
 * @property-read Collection<int, Supplier> $suppliers
 */
class SupplierGroup extends Model implements SupplierGroupContract
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_supplier_group_id',
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
        return (config('erp-buying.table_prefix') ?? '').'supplier_groups';
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::supplierGroup(), 'parent_supplier_group_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ModelResolver::supplierGroup(), 'parent_supplier_group_id');
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(ModelResolver::supplier(), 'supplier_group_id');
    }

    /** @param  Builder<static>  $query */
    public function scopeGroups(Builder $query): Builder
    {
        return $query->where('is_group', true);
    }
}
