<?php

namespace JeffersonGoncalves\Erp\Stock\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Stock\Enums\SerialNoStatus;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver;

/**
 * @property int $id
 * @property string $serial_no
 * @property int $item_id
 * @property int|null $warehouse_id
 * @property SerialNoStatus $status
 * @property float $purchase_rate
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Item|null $item
 * @property-read Warehouse|null $warehouse
 */
class SerialNo extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_no',
        'item_id',
        'warehouse_id',
        'status',
        'purchase_rate',
    ];

    protected $attributes = [
        'status' => 'Active',
        'purchase_rate' => 0,
    ];

    protected $casts = [
        'status' => SerialNoStatus::class,
        'purchase_rate' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-stock.table_prefix') ?? '').'serial_nos';
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::item(), 'item_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::warehouse(), 'warehouse_id');
    }
}
