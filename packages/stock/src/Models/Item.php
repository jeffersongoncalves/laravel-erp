<?php

namespace JeffersonGoncalves\Erp\Stock\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Support\ModelResolver as CoreModelResolver;
use JeffersonGoncalves\Erp\Stock\Enums\ValuationMethod;
use JeffersonGoncalves\Erp\Stock\Models\Contracts\ItemContract;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver;

/**
 * @property int $id
 * @property string $item_code
 * @property string $item_name
 * @property string|null $item_group
 * @property string|null $description
 * @property int|null $stock_uom_id
 * @property bool $is_stock_item
 * @property ValuationMethod|null $valuation_method
 * @property float $standard_rate
 * @property int|null $default_warehouse_id
 * @property int|null $brand_id
 * @property bool $has_batch_no
 * @property bool $has_serial_no
 * @property bool $disabled
 * @property string|null $image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Warehouse|null $defaultWarehouse
 */
class Item extends Model implements ItemContract
{
    use HasFactory;

    protected $fillable = [
        'item_code',
        'item_name',
        'item_group',
        'description',
        'stock_uom_id',
        'is_stock_item',
        'valuation_method',
        'standard_rate',
        'default_warehouse_id',
        'brand_id',
        'has_batch_no',
        'has_serial_no',
        'disabled',
        'image',
    ];

    protected $attributes = [
        'is_stock_item' => true,
        'standard_rate' => 0,
        'has_batch_no' => false,
        'has_serial_no' => false,
        'disabled' => false,
    ];

    protected $casts = [
        'is_stock_item' => 'boolean',
        'valuation_method' => ValuationMethod::class,
        'standard_rate' => 'float',
        'has_batch_no' => 'boolean',
        'has_serial_no' => 'boolean',
        'disabled' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-stock.table_prefix') ?? '').'items';
    }

    public function stockUom(): BelongsTo
    {
        return $this->belongsTo(CoreModelResolver::uom(), 'stock_uom_id');
    }

    public function defaultWarehouse(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::warehouse(), 'default_warehouse_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(CoreModelResolver::brand(), 'brand_id');
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeStockItems(Builder $query): Builder
    {
        return $query->where('is_stock_item', true);
    }
}
