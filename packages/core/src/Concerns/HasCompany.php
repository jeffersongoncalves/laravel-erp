<?php

namespace JeffersonGoncalves\Erp\Core\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use JeffersonGoncalves\Erp\Core\Support\ModelResolver;

/**
 * Associates a model with a company via the `company_id` column.
 *
 * @mixin Model
 */
trait HasCompany
{
    public function company(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::company(), 'company_id');
    }

    /**
     * @param  Builder<Model>  $query
     * @return Builder<Model>
     */
    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query->where('company_id', $companyId);
    }
}
