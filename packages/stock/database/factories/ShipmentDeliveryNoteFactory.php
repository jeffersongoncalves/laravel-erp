<?php

namespace JeffersonGoncalves\Erp\Stock\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Stock\Models\DeliveryNote;
use JeffersonGoncalves\Erp\Stock\Models\Shipment;
use JeffersonGoncalves\Erp\Stock\Models\ShipmentDeliveryNote;

/** @extends Factory<ShipmentDeliveryNote> */
class ShipmentDeliveryNoteFactory extends Factory
{
    protected $model = ShipmentDeliveryNote::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'shipment_id' => Shipment::factory(),
            'delivery_note_id' => DeliveryNote::factory(),
        ];
    }
}
