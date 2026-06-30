<?php

namespace JeffersonGoncalves\Erp\Hr\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface SalaryStructureContract
{
    public function company(): BelongsTo;

    public function components(): HasMany;

    public function assignments(): HasMany;
}
