<?php

namespace JeffersonGoncalves\Erp\Core\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface DepartmentContract
{
    public function company(): BelongsTo;

    public function parent(): BelongsTo;

    public function children(): HasMany;
}
