<?php

namespace JeffersonGoncalves\Erp\Stock\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver;

/**
 * @property int $id
 * @property int $packing_slip_id
 * @property int $item_id
 * @property float $qty
 * @property string|null $batch_no
 * @property string|null $serial_no
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read PackingSlip|null $packingSlip
 * @property-read Item|null $item
 */
class PackingSlipItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'packing_slip_id',
        'item_id',
        'qty',
        'batch_no',
        'serial_no',
    ];

    protected $attributes = [
        'qty' => 0,
    ];

    protected $casts = [
        'qty' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-stock.table_prefix') ?? '').'packing_slip_items';
    }

    public function packingSlip(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::packingSlip(), 'packing_slip_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::item(), 'item_id');
    }
}
