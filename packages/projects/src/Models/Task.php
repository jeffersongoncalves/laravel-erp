<?php

namespace JeffersonGoncalves\Erp\Projects\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Projects\Enums\TaskPriority;
use JeffersonGoncalves\Erp\Projects\Enums\TaskStatus;
use JeffersonGoncalves\Erp\Projects\Models\Contracts\TaskContract;
use JeffersonGoncalves\Erp\Projects\Support\ModelResolver;

/**
 * @property int $id
 * @property string $subject
 * @property int|null $project_id
 * @property TaskStatus $status
 * @property TaskPriority $priority
 * @property int|null $parent_task_id
 * @property bool $is_group
 * @property Carbon|null $exp_start_date
 * @property Carbon|null $exp_end_date
 * @property float $progress
 * @property float $expected_time
 * @property float $actual_time
 * @property int|null $company_id
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Project|null $project
 * @property-read Task|null $parentTask
 * @property-read Collection<int, Task> $childTasks
 */
class Task extends Model implements TaskContract
{
    use HasCompany;
    use HasFactory;

    protected $fillable = [
        'subject',
        'project_id',
        'status',
        'priority',
        'parent_task_id',
        'is_group',
        'exp_start_date',
        'exp_end_date',
        'progress',
        'expected_time',
        'actual_time',
        'company_id',
        'description',
    ];

    protected $attributes = [
        'status' => 'Open',
        'priority' => 'Medium',
        'is_group' => false,
        'progress' => 0,
        'expected_time' => 0,
        'actual_time' => 0,
    ];

    protected $casts = [
        'status' => TaskStatus::class,
        'priority' => TaskPriority::class,
        'is_group' => 'boolean',
        'exp_start_date' => 'date',
        'exp_end_date' => 'date',
        'progress' => 'float',
        'expected_time' => 'float',
        'actual_time' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-projects.table_prefix') ?? '').'tasks';
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::project(), 'project_id');
    }

    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::task(), 'parent_task_id');
    }

    public function childTasks(): HasMany
    {
        return $this->hasMany(ModelResolver::task(), 'parent_task_id');
    }
}
