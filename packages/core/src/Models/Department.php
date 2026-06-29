<?php

namespace JeffersonGoncalves\Erp\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Models\Contracts\DepartmentContract;
use JeffersonGoncalves\Erp\Core\Support\ModelResolver;

/**
 * @property int $id
 * @property string $name
 * @property int|null $company_id
 * @property int|null $parent_department_id
 * @property bool $is_group
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company|null $company
 * @property-read Department|null $parent
 * @property-read Collection<int, Department> $children
 */
class Department extends Model implements DepartmentContract
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'name',
        'company_id',
        'parent_department_id',
        'is_group',
    ];

    protected $casts = [
        'is_group' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-core.table_prefix') ?? '').'departments';
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::department(), 'parent_department_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ModelResolver::department(), 'parent_department_id');
    }

    /** @param  Builder<static>  $query */
    public function scopeGroups(Builder $query): Builder
    {
        return $query->where('is_group', true);
    }
}
