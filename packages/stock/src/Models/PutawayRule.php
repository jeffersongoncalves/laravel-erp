<?php

namespace JeffersonGoncalves\Erp\Stock\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Support\ModelResolver as CoreModelResolver;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver;

/**
 * Defines how much of an item a warehouse can hold, used to suggest putaway
 * allocations across warehouses ordered by priority.
 *
 * @property int $id
 * @property int $item_id
 * @property int $warehouse_id
 * @property int|null $company_id
 * @property float $capacity
 * @property int|null $stock_uom_id
 * @property int $priority
 * @property bool $disabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Item|null $item
 * @property-read Warehouse|null $warehouse
 */
class PutawayRule extends Model
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'item_id',
        'warehouse_id',
        'company_id',
        'capacity',
        'stock_uom_id',
        'priority',
        'disabled',
    ];

    protected $attributes = [
        'capacity' => 0,
        'priority' => 1,
        'disabled' => false,
    ];

    protected $casts = [
        'capacity' => 'float',
        'priority' => 'integer',
        'disabled' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-stock.table_prefix') ?? '').'putaway_rules';
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::item(), 'item_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::warehouse(), 'warehouse_id');
    }

    public function stockUom(): BelongsTo
    {
        return $this->belongsTo(CoreModelResolver::uom(), 'stock_uom_id');
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('disabled', false);
    }
}
