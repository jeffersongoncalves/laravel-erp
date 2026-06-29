<?php

namespace JeffersonGoncalves\Erp\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Models\Contracts\BrandContract;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Brand extends Model implements BrandContract
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function getTable(): string
    {
        return (config('erp-core.table_prefix') ?? '').'brands';
    }
}
