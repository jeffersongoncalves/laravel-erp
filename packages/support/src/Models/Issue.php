<?php

namespace JeffersonGoncalves\Erp\Support\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Support\Enums\AgreementStatus;
use JeffersonGoncalves\Erp\Support\Enums\IssuePriority;
use JeffersonGoncalves\Erp\Support\Enums\IssueStatus;
use JeffersonGoncalves\Erp\Support\Support\ModelResolver;

/**
 * @property int $id
 * @property string $subject
 * @property string $party_type
 * @property int|null $party_id
 * @property string|null $customer_name
 * @property string|null $raised_by
 * @property IssueStatus $status
 * @property IssuePriority $priority
 * @property int|null $issue_type_id
 * @property int|null $service_level_agreement_id
 * @property AgreementStatus|null $agreement_status
 * @property Carbon|null $opening_date
 * @property Carbon|null $resolution_date
 * @property Carbon|null $first_responded_on
 * @property int|null $company_id
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read IssueType|null $issueType
 * @property-read ServiceLevelAgreement|null $serviceLevelAgreement
 */
class Issue extends Model
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'subject',
        'party_type',
        'party_id',
        'customer_name',
        'raised_by',
        'status',
        'priority',
        'issue_type_id',
        'service_level_agreement_id',
        'agreement_status',
        'opening_date',
        'resolution_date',
        'first_responded_on',
        'company_id',
        'description',
    ];

    protected $attributes = [
        'party_type' => 'Customer',
        'status' => 'Open',
        'priority' => 'Medium',
    ];

    protected $casts = [
        'status' => IssueStatus::class,
        'priority' => IssuePriority::class,
        'agreement_status' => AgreementStatus::class,
        'opening_date' => 'date',
        'resolution_date' => 'datetime',
        'first_responded_on' => 'datetime',
    ];

    public function getTable(): string
    {
        return (config('erp-support.table_prefix') ?? '').'issues';
    }

    public function issueType(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::issueType(), 'issue_type_id');
    }

    public function serviceLevelAgreement(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::serviceLevelAgreement(), 'service_level_agreement_id');
    }

    /** @param  Builder<static>  $query */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            IssueStatus::Resolved->value,
            IssueStatus::Closed->value,
        ]);
    }
}
