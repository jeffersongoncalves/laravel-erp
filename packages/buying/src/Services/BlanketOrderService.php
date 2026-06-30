<?php

namespace JeffersonGoncalves\Erp\Buying\Services;

use DomainException;
use JeffersonGoncalves\Erp\Buying\Models\BlanketOrderItem;

/**
 * Tracks draw-downs of agreed quantities against a blanket order.
 */
class BlanketOrderService
{
    /**
     * The quantity still available to order against the agreement line.
     */
    public function availableQty(BlanketOrderItem $item): float
    {
        return (float) $item->qty - (float) $item->ordered_qty;
    }

    /**
     * Record that a quantity has been ordered against the agreement line.
     *
     * @throws DomainException when the order would exceed the agreed quantity
     */
    public function recordOrdered(BlanketOrderItem $item, float $qty): void
    {
        if ((float) $item->ordered_qty + $qty > (float) $item->qty) {
            throw new DomainException('Ordered quantity exceeds the blanket order quantity');
        }

        $item->ordered_qty = (float) $item->ordered_qty + $qty;
        $item->save();
    }
}
