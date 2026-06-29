<?php

namespace JeffersonGoncalves\Erp\Manufacturing\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Support\ModelResolver as CoreModelResolver;
use JeffersonGoncalves\Erp\Manufacturing\Support\ModelResolver;

/**
 * A bill of materials: the list of raw materials and operations required to
 * produce one (or a multiple of) a finished item.
 *
 * The BOM is a master record, not a submittable document. Its cost columns are
 * recomputed from its child rows whenever an item or operation line changes.
 *
 * @property int $id
 * @property string $item_code
 * @property string|null $item_name
 * @property float $quantity
 * @property int|null $uom_id
 * @property int|null $company_id
 * @property bool $is_active
 * @property bool $is_default
 * @property bool $with_operations
 * @property string $currency
 * @property float $total_cost
 * @property float $operating_cost
 * @property float $raw_material_cost
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, BomItem> $items
 * @property-read Collection<int, BomOperation> $operations
 */
class Bom extends Model
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'item_code',
        'item_name',
        'quantity',
        'uom_id',
        'company_id',
        'is_active',
        'is_default',
        'with_operations',
        'currency',
        'total_cost',
        'operating_cost',
        'raw_material_cost',
    ];

    protected $attributes = [
        'quantity' => 1,
        'is_active' => true,
        'is_default' => false,
        'with_operations' => false,
        'currency' => 'USD',
        'total_cost' => 0,
        'operating_cost' => 0,
        'raw_material_cost' => 0,
    ];

    protected $casts = [
        'quantity' => 'float',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'with_operations' => 'boolean',
        'total_cost' => 'float',
        'operating_cost' => 'float',
        'raw_material_cost' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-manufacturing.table_prefix') ?? '').'boms';
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(CoreModelResolver::uom(), 'uom_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ModelResolver::bomItem(), 'bom_id');
    }

    public function operations(): HasMany
    {
        return $this->hasMany(ModelResolver::bomOperation(), 'bom_id');
    }

    /**
     * Recompute the cost columns from the child item and operation rows.
     */
    public function recalculateCosts(): void
    {
        if (! $this->exists) {
            return;
        }

        $rawMaterialCost = (float) $this->items()->sum('amount');
        $operatingCost = (float) $this->operations()->sum('operating_cost');

        $this->raw_material_cost = $rawMaterialCost;
        $this->operating_cost = $operatingCost;
        $this->total_cost = $rawMaterialCost + $operatingCost;
        $this->save();
    }
}
