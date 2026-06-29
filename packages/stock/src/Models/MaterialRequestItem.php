<?php

namespace JeffersonGoncalves\Erp\Stock\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver;

/**
 * @property int $id
 * @property int $material_request_id
 * @property int $item_id
 * @property float $qty
 * @property int|null $warehouse_id
 * @property int|null $uom_id
 * @property float $rate
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read MaterialRequest|null $materialRequest
 * @property-read Item|null $item
 */
class MaterialRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_request_id',
        'item_id',
        'qty',
        'warehouse_id',
        'uom_id',
        'rate',
    ];

    protected $attributes = [
        'qty' => 0,
        'rate' => 0,
    ];

    protected $casts = [
        'qty' => 'float',
        'rate' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-stock.table_prefix') ?? '').'material_request_items';
    }

    public function materialRequest(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::materialRequest(), 'material_request_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::item(), 'item_id');
    }
}
