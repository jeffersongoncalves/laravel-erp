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
 * @property int $delivery_note_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Shipment|null $shipment
 * @property-read DeliveryNote|null $deliveryNote
 */
class ShipmentDeliveryNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'delivery_note_id',
    ];

    public function getTable(): string
    {
        return (config('erp-stock.table_prefix') ?? '').'shipment_delivery_notes';
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::shipment(), 'shipment_id');
    }

    public function deliveryNote(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::deliveryNote(), 'delivery_note_id');
    }
}
