<?php

namespace JeffersonGoncalves\Erp\Buying\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Buying\Enums\BlanketOrderType;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;

/**
 * A blanket order agreed with a party for a range of dates. A commitment
 * document — submittable with no ledger impact; downstream purchase or sales
 * orders draw down against its agreed quantities.
 *
 * @property int $id
 * @property BlanketOrderType $order_type
 * @property string $party_type
 * @property int|null $party_id
 * @property Carbon $from_date
 * @property Carbon $to_date
 * @property int|null $company_id
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, BlanketOrderItem> $items
 */
class BlanketOrder extends Model implements SubmittableDocument
{
    use HasCompany;
    use HasFactory;
    use IsSubmittable;

    protected $fillable = [
        'order_type',
        'party_type',
        'party_id',
        'from_date',
        'to_date',
        'company_id',
        'docstatus',
    ];

    protected $attributes = [
        'order_type' => BlanketOrderType::Purchasing->value,
        'party_type' => 'Supplier',
        'docstatus' => 0,
    ];

    protected $casts = [
        'order_type' => BlanketOrderType::class,
        'from_date' => 'date',
        'to_date' => 'date',
        'docstatus' => DocStatus::class,
    ];

    public function getTable(): string
    {
        return (config('erp-buying.table_prefix') ?? '').'blanket_orders';
    }

    public function items(): HasMany
    {
        return $this->hasMany(BlanketOrderItem::class, 'blanket_order_id');
    }
}
