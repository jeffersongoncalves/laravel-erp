<?php

namespace JeffersonGoncalves\Erp\Selling\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface CustomerContract
{
    public function customerGroup(): BelongsTo;

    public function defaultPriceList(): BelongsTo;

    public function addresses(): MorphMany;

    public function contacts(): MorphMany;
}
