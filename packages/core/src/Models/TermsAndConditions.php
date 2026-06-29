<?php

namespace JeffersonGoncalves\Erp\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Models\Contracts\TermsAndConditionsContract;

/**
 * @property int $id
 * @property string $name
 * @property string $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class TermsAndConditions extends Model implements TermsAndConditionsContract
{
    use HasFactory;

    protected $fillable = [
        'name',
        'content',
    ];

    public function getTable(): string
    {
        return (config('erp-core.table_prefix') ?? '').'terms_and_conditions';
    }
}
