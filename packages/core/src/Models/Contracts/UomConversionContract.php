<?php

namespace JeffersonGoncalves\Erp\Core\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface UomConversionContract
{
    public function fromUom(): BelongsTo;

    public function toUom(): BelongsTo;
}
