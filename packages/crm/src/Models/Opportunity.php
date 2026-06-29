<?php

namespace JeffersonGoncalves\Erp\Crm\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Crm\Enums\OpportunityStatus;
use JeffersonGoncalves\Erp\Crm\Enums\OpportunityType;
use JeffersonGoncalves\Erp\Crm\Support\ModelResolver;

/**
 * @property int $id
 * @property string $opportunity_from
 * @property int|null $lead_id
 * @property string|null $party_type
 * @property int|null $party_id
 * @property string|null $party_name
 * @property OpportunityStatus $status
 * @property OpportunityType $opportunity_type
 * @property float $opportunity_amount
 * @property float $probability
 * @property Carbon|null $expected_closing
 * @property int|null $campaign_id
 * @property int|null $company_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Lead|null $lead
 * @property-read Campaign|null $campaign
 * @property-read Collection<int, OpportunityItem> $items
 */
class Opportunity extends Model
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'opportunity_from',
        'lead_id',
        'party_type',
        'party_id',
        'party_name',
        'status',
        'opportunity_type',
        'opportunity_amount',
        'probability',
        'expected_closing',
        'campaign_id',
        'company_id',
    ];

    protected $attributes = [
        'opportunity_from' => 'Lead',
        'status' => 'Open',
        'opportunity_type' => 'Sales',
        'opportunity_amount' => 0,
        'probability' => 0,
    ];

    protected $casts = [
        'status' => OpportunityStatus::class,
        'opportunity_type' => OpportunityType::class,
        'opportunity_amount' => 'float',
        'probability' => 'float',
        'expected_closing' => 'date',
    ];

    public function getTable(): string
    {
        return (config('erp-crm.table_prefix') ?? '').'opportunities';
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::lead(), 'lead_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::campaign(), 'campaign_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ModelResolver::opportunityItem(), 'opportunity_id');
    }

    public function calculateAmount(): void
    {
        $this->opportunity_amount = $this->exists ? (float) $this->items()->sum('amount') : 0.0;
    }
}
