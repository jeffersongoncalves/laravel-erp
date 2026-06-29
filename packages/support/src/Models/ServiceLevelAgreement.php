<?php

namespace JeffersonGoncalves\Erp\Support\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Support\Enums\IssuePriority;
use JeffersonGoncalves\Erp\Support\Support\ModelResolver;

/**
 * @property int $id
 * @property string $name
 * @property bool $default_sla
 * @property bool $enabled
 * @property IssuePriority $default_priority
 * @property int|null $company_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, ServiceLevelPriority> $priorities
 * @property-read Collection<int, Issue> $issues
 */
class ServiceLevelAgreement extends Model
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'name',
        'default_sla',
        'enabled',
        'default_priority',
        'company_id',
    ];

    protected $attributes = [
        'default_sla' => false,
        'enabled' => true,
        'default_priority' => 'Medium',
    ];

    protected $casts = [
        'default_sla' => 'boolean',
        'enabled' => 'boolean',
        'default_priority' => IssuePriority::class,
    ];

    public function getTable(): string
    {
        return (config('erp-support.table_prefix') ?? '').'service_level_agreements';
    }

    public function priorities(): HasMany
    {
        return $this->hasMany(ModelResolver::serviceLevelPriority(), 'service_level_agreement_id');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(ModelResolver::issue(), 'service_level_agreement_id');
    }
}
