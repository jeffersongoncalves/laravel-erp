<?php

namespace JeffersonGoncalves\Erp\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Enums\AddressType;
use JeffersonGoncalves\Erp\Core\Models\Contracts\AddressContract;

/**
 * @property int $id
 * @property AddressType $address_type
 * @property string $address_line1
 * @property string|null $address_line2
 * @property string $city
 * @property string|null $state
 * @property string $country
 * @property string|null $pincode
 * @property string $addressable_type
 * @property int $addressable_id
 * @property bool $is_primary
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model $addressable
 */
class Address extends Model implements AddressContract
{
    use HasFactory;

    protected $fillable = [
        'address_type',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'country',
        'pincode',
        'is_primary',
    ];

    protected $casts = [
        'address_type' => AddressType::class,
        'is_primary' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-core.table_prefix') ?? '').'addresses';
    }

    public function addressable(): MorphTo
    {
        return $this->morphTo('addressable');
    }

    /** @param  Builder<static>  $query */
    public function scopePrimary(Builder $query): Builder
    {
        return $query->where('is_primary', true);
    }
}
