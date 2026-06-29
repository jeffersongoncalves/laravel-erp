<?php

use JeffersonGoncalves\Erp\Stock\Enums\ValuationMethod;
use JeffersonGoncalves\Erp\Stock\Models\Item;
use JeffersonGoncalves\Erp\Stock\Tests\TestCase;

uses(TestCase::class)->in('Unit', 'Feature');

/**
 * Create a stock item, optionally pinned to a valuation method.
 *
 * @param  array<string, mixed>  $attributes
 */
function stockItem(array $attributes = [], ?ValuationMethod $method = null): Item
{
    $factory = Item::factory();

    if ($method !== null) {
        $factory = $factory->valuation($method);
    }

    return $factory->create($attributes);
}
