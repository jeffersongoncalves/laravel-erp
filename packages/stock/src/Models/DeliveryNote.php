<?php

namespace JeffersonGoncalves\Erp\Stock\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Concerns\HasNamingSeries;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\PostsToLedger;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Stock\Concerns\ResolvesStockGlAccounts;
use JeffersonGoncalves\Erp\Stock\Contracts\PostsStockLedger;
use JeffersonGoncalves\Erp\Stock\Services\StockLedgerService;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver;

/**
 * Ships stock out to a customer: an outbound movement whose value is posted as
 * the cost of goods sold against the inventory account.
 *
 * @property int $id
 * @property string|null $naming_series
 * @property string $party_type
 * @property int|null $party_id
 * @property string $customer_name
 * @property Carbon $posting_date
 * @property int|null $company_id
 * @property int|null $set_warehouse_id
 * @property float $total_qty
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Warehouse|null $setWarehouse
 * @property-read Collection<int, DeliveryNoteItem> $items
 */
class DeliveryNote extends Model implements PostsStockLedger, PostsToLedger, SubmittableDocument
{
    use HasCompany;
    use HasFactory;
    use HasNamingSeries;
    use IsSubmittable;
    use ResolvesStockGlAccounts;

    /** The cost-of-goods-sold account the outbound value is debited to. */
    public ?int $counterAccountId = null;

    protected $fillable = [
        'naming_series',
        'party_type',
        'party_id',
        'customer_name',
        'posting_date',
        'company_id',
        'set_warehouse_id',
        'total_qty',
        'docstatus',
    ];

    protected $attributes = [
        'party_type' => 'Customer',
        'total_qty' => 0,
        'docstatus' => 0,
    ];

    protected $casts = [
        'posting_date' => 'datetime',
        'total_qty' => 'float',
        'docstatus' => DocStatus::class,
    ];

    protected static function booted(): void
    {
        static::saving(function (DeliveryNote $note): void {
            if ($note->docstatus === DocStatus::Draft) {
                $note->calculateTotals();
            }
        });
    }

    public function getTable(): string
    {
        return (config('erp-stock.table_prefix') ?? '').'delivery_notes';
    }

    public function setWarehouse(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::warehouse(), 'set_warehouse_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ModelResolver::deliveryNoteItem(), 'delivery_note_id');
    }

    public function calculateTotals(): void
    {
        $this->total_qty = $this->exists ? (float) $this->items()->sum('qty') : 0.0;
    }

    public function postLedgerEntries(): void
    {
        app(StockLedgerService::class)->post($this, $this->buildMovements());
    }

    public function reverseLedgerEntries(): void
    {
        app(StockLedgerService::class)->reverse($this);
    }

    public function stockGlAccounts(): array
    {
        return [
            'stock_account_id' => $this->warehouseAccountId($this->primaryWarehouseId()),
            'counter_account_id' => $this->counterAccountId,
        ];
    }

    /**
     * @return list<array{item_id: int, warehouse_id: int, actual_qty: float, incoming_rate: float, posting_date: mixed}>
     */
    protected function buildMovements(): array
    {
        $movements = [];

        foreach ($this->items as $item) {
            $warehouseId = $item->warehouse_id ?? $this->set_warehouse_id;

            if ($warehouseId === null) {
                continue;
            }

            $movements[] = [
                'item_id' => (int) $item->item_id,
                'warehouse_id' => (int) $warehouseId,
                'actual_qty' => -1 * (float) $item->qty,
                'incoming_rate' => 0.0,
                'posting_date' => $this->posting_date,
            ];
        }

        return $movements;
    }

    protected function primaryWarehouseId(): ?int
    {
        if ($this->set_warehouse_id !== null) {
            return (int) $this->set_warehouse_id;
        }

        $first = $this->items->first();

        return $first?->warehouse_id !== null ? (int) $first->warehouse_id : null;
    }
}
