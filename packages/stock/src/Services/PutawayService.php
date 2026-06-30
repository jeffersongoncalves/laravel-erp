<?php

namespace JeffersonGoncalves\Erp\Stock\Services;

use JeffersonGoncalves\Erp\Stock\Support\ModelResolver;

/**
 * Suggests where to put away an incoming quantity of an item.
 *
 * Allocations are spread across the item's enabled putaway rules ordered by
 * priority, each capped by the warehouse's remaining free capacity (the rule
 * capacity minus the current bin balance for that item and warehouse). Any
 * quantity that cannot be placed is returned as a trailing 'unassigned' entry.
 */
class PutawayService
{
    /** Tolerance below which a quantity is treated as zero. */
    private const EPSILON = 0.000000001;

    /**
     * @return list<array{warehouse_id: int, qty: float}|array{unassigned: float}>
     */
    public function suggest(int $itemId, float $qty): array
    {
        $remaining = $qty;
        $allocations = [];

        $rules = ModelResolver::putawayRule()::query()
            ->where('item_id', $itemId)
            ->where('disabled', false)
            ->orderBy('priority')
            ->get();

        foreach ($rules as $rule) {
            if ($remaining <= self::EPSILON) {
                break;
            }

            $warehouseId = (int) $rule->getAttribute('warehouse_id');
            $capacity = (float) $rule->getAttribute('capacity');
            $available = $capacity - $this->currentQty($itemId, $warehouseId);

            if ($available <= self::EPSILON) {
                continue;
            }

            $allocated = min($remaining, $available);

            $allocations[] = [
                'warehouse_id' => $warehouseId,
                'qty' => $allocated,
            ];

            $remaining -= $allocated;
        }

        $allocations[] = ['unassigned' => max($remaining, 0.0)];

        return $allocations;
    }

    private function currentQty(int $itemId, int $warehouseId): float
    {
        $bin = ModelResolver::bin()::query()
            ->where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->first();

        return $bin !== null ? (float) $bin->getAttribute('actual_qty') : 0.0;
    }
}
