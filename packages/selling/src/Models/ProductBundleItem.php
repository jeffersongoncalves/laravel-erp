<?php

namespace JeffersonGoncalves\Erp\Selling\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Selling\Support\ModelResolver;

/**
 * @property int $id
 * @property int $product_bundle_id
 * @property string $item_code
 * @property float $qty
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ProductBundle|null $productBundle
 */
class ProductBundleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_bundle_id',
        'item_code',
        'qty',
    ];

    protected $attributes = [
        'qty' => 1,
    ];

    protected $casts = [
        'qty' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-selling.table_prefix') ?? '').'product_bundle_items';
    }

    public function productBundle(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::productBundle(), 'product_bundle_id');
    }
}
