<?php

namespace JeffersonGoncalves\Erp\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property int $max_leaves_allowed
 * @property bool $is_paid
 * @property bool $allow_negative
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'max_leaves_allowed',
        'is_paid',
        'allow_negative',
    ];

    protected $attributes = [
        'max_leaves_allowed' => 0,
        'is_paid' => true,
        'allow_negative' => false,
    ];

    protected $casts = [
        'max_leaves_allowed' => 'integer',
        'is_paid' => 'boolean',
        'allow_negative' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-hr.table_prefix') ?? '').'leave_types';
    }
}
