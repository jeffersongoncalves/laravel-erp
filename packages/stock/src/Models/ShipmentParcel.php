<?php

namespace JeffersonGoncalves\Erp\Stock\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver;

/**
 * @property int $id
 * @property int $shipment_id
 * @property float $length
 * @property float $width
 * @property float $height
 * @property float $weight
 * @property int $count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Shipment|null $shipment
 */
class ShipmentParcel extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'length',
        'width',
        'height',
        'weight',
        'count',
    ];

    protected $attributes = [
        'length' => 0,
        'width' => 0,
        'height' => 0,
        'weight' => 0,
        'count' => 1,
    ];

    protected $casts = [
        'length' => 'float',
        'width' => 'float',
        'height' => 'float',
        'weight' => 'float',
        'count' => 'integer',
    ];

    public function getTable(): string
    {
        return (config('erp-stock.table_prefix') ?? '').'shipment_parcels';
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::shipment(), 'shipment_id');
    }
}
