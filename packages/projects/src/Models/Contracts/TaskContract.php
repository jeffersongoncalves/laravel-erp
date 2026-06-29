<?php

namespace JeffersonGoncalves\Erp\Projects\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface TaskContract
{
    public function project(): BelongsTo;

    public function parentTask(): BelongsTo;

    public function childTasks(): HasMany;
}
