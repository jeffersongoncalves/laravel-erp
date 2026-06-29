<?php

namespace JeffersonGoncalves\Erp\Stock\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver as AccountingModelResolver;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Stock\Models\Contracts\WarehouseContract;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver;

/**
 * @property int $id
 * @property string $name
 * @property int|null $parent_warehouse_id
 * @property bool $is_group
 * @property int|null $company_id
 * @property int|null $account_id
 * @property bool $disabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Warehouse|null $parent
 * @property-read Collection<int, Warehouse> $children
 */
class Warehouse extends Model implements WarehouseContract
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_warehouse_id',
        'is_group',
        'company_id',
        'account_id',
        'disabled',
    ];

    protected $attributes = [
        'is_group' => false,
        'disabled' => false,
    ];

    protected $casts = [
        'is_group' => 'boolean',
        'disabled' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-stock.table_prefix') ?? '').'warehouses';
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::warehouse(), 'parent_warehouse_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ModelResolver::warehouse(), 'parent_warehouse_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(AccountingModelResolver::account(), 'account_id');
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeLeaf(Builder $query): Builder
    {
        return $query->where('is_group', false);
    }
}
