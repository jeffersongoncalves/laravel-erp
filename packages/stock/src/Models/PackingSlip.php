<?php

namespace JeffersonGoncalves\Erp\Stock\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\PostsToLedger;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver;

/**
 * Packs the items of a delivery note into a range of cases for shipping.
 * Submitting a packing slip posts nothing to the ledger.
 *
 * @property int $id
 * @property int $delivery_note_id
 * @property int $from_case_no
 * @property int $to_case_no
 * @property float $net_weight
 * @property float $gross_weight
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DeliveryNote|null $deliveryNote
 * @property-read Collection<int, PackingSlipItem> $items
 */
class PackingSlip extends Model implements PostsToLedger, SubmittableDocument
{
    use HasFactory;
    use IsSubmittable;

    protected $fillable = [
        'delivery_note_id',
        'from_case_no',
        'to_case_no',
        'net_weight',
        'gross_weight',
        'docstatus',
    ];

    protected $attributes = [
        'from_case_no' => 1,
        'to_case_no' => 1,
        'net_weight' => 0,
        'gross_weight' => 0,
        'docstatus' => 0,
    ];

    protected $casts = [
        'from_case_no' => 'integer',
        'to_case_no' => 'integer',
        'net_weight' => 'float',
        'gross_weight' => 'float',
        'docstatus' => DocStatus::class,
    ];

    public function getTable(): string
    {
        return (config('erp-stock.table_prefix') ?? '').'packing_slips';
    }

    public function deliveryNote(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::deliveryNote(), 'delivery_note_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ModelResolver::packingSlipItem(), 'packing_slip_id');
    }

    /**
     * Packing slips are logistics documents, not accounting documents:
     * submitting one posts nothing to the general ledger or the stock ledger.
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
