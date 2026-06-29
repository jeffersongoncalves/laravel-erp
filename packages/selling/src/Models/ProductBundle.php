<?php

namespace JeffersonGoncalves\Erp\Selling\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Selling\Support\ModelResolver;

/**
 * @property int $id
 * @property string $new_item_code
 * @property string|null $description
 * @property bool $disabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, ProductBundleItem> $items
 */
class ProductBundle extends Model
{
    use HasFactory;

    protected $fillable = [
        'new_item_code',
        'description',
        'disabled',
    ];

    protected $attributes = [
        'disabled' => false,
    ];

    protected $casts = [
        'disabled' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-selling.table_prefix') ?? '').'product_bundles';
    }

    public function items(): HasMany
    {
        return $this->hasMany(ModelResolver::productBundleItem(), 'product_bundle_id');
    }
}
