<?php

namespace JeffersonGoncalves\Erp\Projects\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Projects\Enums\ProjectStatus;
use JeffersonGoncalves\Erp\Projects\Models\Contracts\ProjectContract;
use JeffersonGoncalves\Erp\Projects\Support\ModelResolver;

/**
 * @property int $id
 * @property string $project_name
 * @property ProjectStatus $status
 * @property string $party_type
 * @property int|null $party_id
 * @property string|null $customer_name
 * @property Carbon|null $expected_start_date
 * @property Carbon|null $expected_end_date
 * @property float $percent_complete
 * @property float $estimated_costing
 * @property float $total_billable_amount
 * @property float $total_billed_amount
 * @property int|null $company_id
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Task> $tasks
 * @property-read Collection<int, Timesheet> $timesheets
 */
class Project extends Model implements ProjectContract
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'project_name',
        'status',
        'party_type',
        'party_id',
        'customer_name',
        'expected_start_date',
        'expected_end_date',
        'percent_complete',
        'estimated_costing',
        'total_billable_amount',
        'total_billed_amount',
        'company_id',
        'notes',
    ];

    protected $attributes = [
        'status' => 'Open',
        'party_type' => 'Customer',
        'percent_complete' => 0,
        'estimated_costing' => 0,
        'total_billable_amount' => 0,
        'total_billed_amount' => 0,
    ];

    protected $casts = [
        'status' => ProjectStatus::class,
        'expected_start_date' => 'date',
        'expected_end_date' => 'date',
        'percent_complete' => 'float',
        'estimated_costing' => 'float',
        'total_billable_amount' => 'float',
        'total_billed_amount' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-projects.table_prefix') ?? '').'projects';
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(ModelResolver::task(), 'project_id');
    }

    public function timesheets(): HasMany
    {
        return $this->hasMany(ModelResolver::timesheet(), 'parent_project_id');
    }
}
