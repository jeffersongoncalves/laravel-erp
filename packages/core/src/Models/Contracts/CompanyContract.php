<?php

namespace JeffersonGoncalves\Erp\Core\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface CompanyContract
{
    public function parent(): BelongsTo;

    public function children(): HasMany;

    public function departments(): HasMany;

    public function addresses(): MorphMany;

    public function contacts(): MorphMany;
}
