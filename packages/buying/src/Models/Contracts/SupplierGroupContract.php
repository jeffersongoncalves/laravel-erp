<?php

namespace JeffersonGoncalves\Erp\Buying\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface SupplierGroupContract
{
    public function parent(): BelongsTo;

    public function children(): HasMany;
}
