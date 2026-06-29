<?php

namespace JeffersonGoncalves\Erp\Stock\Concerns;

use Illuminate\Database\Eloquent\Model;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver;

/**
 * Resolves the inventory GL account configured on a warehouse.
 *
 * @mixin Model
 */
trait ResolvesStockGlAccounts
{
    protected function warehouseAccountId(?int $warehouseId): ?int
    {
        if ($warehouseId === null) {
            return null;
        }

        $warehouse = ModelResolver::warehouse()::query()->find($warehouseId);

        $accountId = $warehouse?->getAttribute('account_id');

        return $accountId !== null ? (int) $accountId : null;
    }
}
