<?php

namespace JeffersonGoncalves\Erp\Selling\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface CustomerGroupContract
{
    public function parent(): BelongsTo;

    public function children(): HasMany;

    public function customers(): HasMany;
}
