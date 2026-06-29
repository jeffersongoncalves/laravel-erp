<?php

namespace JeffersonGoncalves\Erp\Buying\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface SupplierContract
{
    public function supplierGroup(): BelongsTo;

    public function addresses(): MorphMany;

    public function contacts(): MorphMany;
}
