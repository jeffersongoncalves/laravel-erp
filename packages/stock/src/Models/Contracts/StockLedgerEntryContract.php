<?php

namespace JeffersonGoncalves\Erp\Stock\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface StockLedgerEntryContract
{
    public function item(): BelongsTo;

    public function warehouse(): BelongsTo;

    public function voucherable(): MorphTo;
}
