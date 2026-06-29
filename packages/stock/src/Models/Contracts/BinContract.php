<?php

namespace JeffersonGoncalves\Erp\Stock\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface BinContract
{
    public function item(): BelongsTo;

    public function warehouse(): BelongsTo;
}
