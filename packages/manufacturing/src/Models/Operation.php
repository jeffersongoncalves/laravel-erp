<?php

namespace JeffersonGoncalves\Erp\Manufacturing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Manufacturing\Support\ModelResolver;

/**
 * @property int $id
 * @property string $name
 * @property int|null $workstation_id
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Workstation|null $workstation
 */
class Operation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'workstation_id',
        'description',
    ];

    public function getTable(): string
    {
        return (config('erp-manufacturing.table_prefix') ?? '').'operations';
    }

    public function workstation(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::workstation(), 'workstation_id');
    }
}
