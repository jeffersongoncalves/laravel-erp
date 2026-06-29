<?php

namespace JeffersonGoncalves\Erp\Crm\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Crm\Support\ModelResolver;

/**
 * @property int $id
 * @property string $name
 * @property string|null $details
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Lead> $leads
 */
class LeadSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'details',
    ];

    public function getTable(): string
    {
        return (config('erp-crm.table_prefix') ?? '').'lead_sources';
    }

    public function leads(): HasMany
    {
        return $this->hasMany(ModelResolver::lead(), 'lead_source_id');
    }
}
