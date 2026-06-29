<?php

namespace JeffersonGoncalves\Erp\Support\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Support\Support\ModelResolver;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Issue> $issues
 */
class IssueType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function getTable(): string
    {
        return (config('erp-support.table_prefix') ?? '').'issue_types';
    }

    public function issues(): HasMany
    {
        return $this->hasMany(ModelResolver::issue(), 'issue_type_id');
    }
}
