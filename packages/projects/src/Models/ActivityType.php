<?php

namespace JeffersonGoncalves\Erp\Projects\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property float $default_costing_rate
 * @property float $default_billing_rate
 * @property bool $disabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ActivityType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'default_costing_rate',
        'default_billing_rate',
        'disabled',
    ];

    protected $attributes = [
        'default_costing_rate' => 0,
        'default_billing_rate' => 0,
        'disabled' => false,
    ];

    protected $casts = [
        'default_costing_rate' => 'float',
        'default_billing_rate' => 'float',
        'disabled' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-projects.table_prefix') ?? '').'activity_types';
    }

    /** @param  Builder<static>  $query */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('disabled', false);
    }
}
