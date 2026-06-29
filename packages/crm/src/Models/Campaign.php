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
 * @property string $campaign_name
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Lead> $leads
 * @property-read Collection<int, Opportunity> $opportunities
 */
class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_name',
        'description',
    ];

    public function getTable(): string
    {
        return (config('erp-crm.table_prefix') ?? '').'campaigns';
    }

    public function leads(): HasMany
    {
        return $this->hasMany(ModelResolver::lead(), 'campaign_id');
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(ModelResolver::opportunity(), 'campaign_id');
    }
}
