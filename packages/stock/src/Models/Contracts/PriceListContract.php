<?php

namespace JeffersonGoncalves\Erp\Stock\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\HasMany;

interface PriceListContract
{
    public function itemPrices(): HasMany;
}
