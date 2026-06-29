<?php

namespace JeffersonGoncalves\Erp\Crm\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Crm\Enums\LeadStatus;
use JeffersonGoncalves\Erp\Crm\Support\ModelResolver;

/**
 * @property int $id
 * @property string $lead_name
 * @property string|null $company_name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $mobile_no
 * @property LeadStatus $status
 * @property int|null $lead_source_id
 * @property int|null $campaign_id
 * @property string|null $territory
 * @property int|null $customer_id
 * @property int|null $company_id
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read LeadSource|null $leadSource
 * @property-read Campaign|null $campaign
 * @property-read Collection<int, Opportunity> $opportunities
 */
class Lead extends Model
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'lead_name',
        'company_name',
        'email',
        'phone',
        'mobile_no',
        'status',
        'lead_source_id',
        'campaign_id',
        'territory',
        'customer_id',
        'company_id',
        'notes',
    ];

    protected $attributes = [
        'status' => 'Lead',
    ];

    protected $casts = [
        'status' => LeadStatus::class,
    ];

    public function getTable(): string
    {
        return (config('erp-crm.table_prefix') ?? '').'leads';
    }

    public function leadSource(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::leadSource(), 'lead_source_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::campaign(), 'campaign_id');
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(ModelResolver::opportunity(), 'lead_id');
    }

    /** @param  Builder<static>  $query */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            LeadStatus::Converted->value,
            LeadStatus::DoNotContact->value,
        ]);
    }
}
