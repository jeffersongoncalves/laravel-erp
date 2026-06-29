<?php

namespace JeffersonGoncalves\Erp\Stock\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface WarehouseContract
{
    public function parent(): BelongsTo;

    public function children(): HasMany;

    public function account(): BelongsTo;
}
