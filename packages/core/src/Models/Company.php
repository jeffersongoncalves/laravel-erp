<?php

namespace JeffersonGoncalves\Erp\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Models\Contracts\CompanyContract;
use JeffersonGoncalves\Erp\Core\Support\ModelResolver;

/**
 * @property int $id
 * @property string $name
 * @property string $abbr
 * @property string $default_currency
 * @property string|null $country
 * @property string|null $tax_id
 * @property int|null $parent_company_id
 * @property bool $is_group
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company|null $parent
 * @property-read Collection<int, Company> $children
 * @property-read Collection<int, Department> $departments
 * @property-read Collection<int, Address> $addresses
 * @property-read Collection<int, Contact> $contacts
 */
class Company extends Model implements CompanyContract
{
    use HasFactory;

    protected $fillable = [
        'name',
        'abbr',
        'default_currency',
        'country',
        'tax_id',
        'parent_company_id',
        'is_group',
    ];

    protected $casts = [
        'is_group' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-core.table_prefix') ?? '').'companies';
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::company(), 'parent_company_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ModelResolver::company(), 'parent_company_id');
    }

    public function departments(): HasMany
    {
        return $this->hasMany(ModelResolver::department(), 'company_id');
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(ModelResolver::address(), 'addressable');
    }

    public function contacts(): MorphMany
    {
        return $this->morphMany(ModelResolver::contact(), 'contactable');
    }

    /** @param  Builder<static>  $query */
    public function scopeGroups(Builder $query): Builder
    {
        return $query->where('is_group', true);
    }

    /** @param  Builder<static>  $query */
    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_company_id');
    }
}
