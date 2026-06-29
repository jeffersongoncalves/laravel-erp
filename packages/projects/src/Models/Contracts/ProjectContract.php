<?php

namespace JeffersonGoncalves\Erp\Projects\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface ProjectContract
{
    public function company(): BelongsTo;

    public function tasks(): HasMany;

    public function timesheets(): HasMany;
}
