<?php

namespace JeffersonGoncalves\Erp\Buying\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Buying\Models\Contracts\SupplierContract;
use JeffersonGoncalves\Erp\Buying\Support\ModelResolver;
use JeffersonGoncalves\Erp\Core\Support\ModelResolver as CoreModelResolver;

/**
 * @property int $id
 * @property string $supplier_name
 * @property int|null $supplier_group_id
 * @property string $supplier_type
 * @property string|null $country
 * @property string $default_currency
 * @property string|null $tax_id
 * @property bool $disabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read SupplierGroup|null $supplierGroup
 */
class Supplier extends Model implements SupplierContract
{
    use HasFactory;

    protected $fillable = [
        'supplier_name',
        'supplier_group_id',
        'supplier_type',
        'country',
        'default_currency',
        'tax_id',
        'disabled',
    ];

    protected $attributes = [
        'supplier_type' => 'Company',
        'default_currency' => 'USD',
        'disabled' => false,
    ];

    protected $casts = [
        'disabled' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-buying.table_prefix') ?? '').'suppliers';
    }

    public function supplierGroup(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::supplierGroup(), 'supplier_group_id');
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(CoreModelResolver::address(), 'addressable');
    }

    public function contacts(): MorphMany
    {
        return $this->morphMany(CoreModelResolver::contact(), 'contactable');
    }

    /** @param  Builder<static>  $query */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('disabled', false);
    }
}
