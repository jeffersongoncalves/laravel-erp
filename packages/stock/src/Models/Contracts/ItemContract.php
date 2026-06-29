<?php

namespace JeffersonGoncalves\Erp\Stock\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface ItemContract
{
    public function stockUom(): BelongsTo;

    public function defaultWarehouse(): BelongsTo;
}
