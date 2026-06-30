<?php

namespace JeffersonGoncalves\Erp\Stock\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\PostsToLedger;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver;

/**
 * A logistics shipment grouping one or more delivery notes into physical
 * parcels for a carrier. Submitting a shipment posts nothing to the ledger.
 *
 * @property int $id
 * @property int|null $company_id
 * @property string $pickup_from_type
 * @property string $delivery_to_type
 * @property string|null $party_type
 * @property int|null $party_id
 * @property Carbon $shipment_date
 * @property float $value_of_goods
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, ShipmentParcel> $parcels
 * @property-read Collection<int, ShipmentDeliveryNote> $deliveryNotes
 */
class Shipment extends Model implements PostsToLedger, SubmittableDocument
{
    use HasCompany;
    use HasFactory;
    use IsSubmittable;

    protected $fillable = [
        'company_id',
        'pickup_from_type',
        'delivery_to_type',
        'party_type',
        'party_id',
        'shipment_date',
        'value_of_goods',
        'docstatus',
    ];

    protected $attributes = [
        'pickup_from_type' => 'Company',
        'delivery_to_type' => 'Customer',
        'value_of_goods' => 0,
        'docstatus' => 0,
    ];

    protected $casts = [
        'shipment_date' => 'date',
        'value_of_goods' => 'float',
        'docstatus' => DocStatus::class,
    ];

    public function getTable(): string
    {
        return (config('erp-stock.table_prefix') ?? '').'shipments';
    }

    public function parcels(): HasMany
    {
        return $this->hasMany(ModelResolver::shipmentParcel(), 'shipment_id');
    }

    public function deliveryNotes(): HasMany
    {
        return $this->hasMany(ModelResolver::shipmentDeliveryNote(), 'shipment_id');
    }

    /**
     * Shipments are logistics documents, not accounting documents: submitting
     * one posts nothing to the general ledger or the stock ledger.
     */
    public function postLedgerEntries(): void
    {
        // No ledger impact.
    }

    public function reverseLedgerEntries(): void
    {
        // No ledger impact.
    }
}
