<?php

namespace JeffersonGoncalves\Erp\Manufacturing\Models;

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
use JeffersonGoncalves\Erp\Manufacturing\Enums\WorkOrderStatus;
use JeffersonGoncalves\Erp\Manufacturing\Services\WorkOrderService;
use JeffersonGoncalves\Erp\Manufacturing\Support\ModelResolver;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver as StockModelResolver;

/**
 * An order to manufacture a quantity of a finished item against a BOM.
 *
 * On submit, the required-materials child rows are populated from the BOM
 * (scaled to the ordered quantity) when they have not been entered manually,
 * and the status advances from Draft to Not Started. The document itself posts
 * nothing to the ledger: the cross-module Manufacture stock entry produced by
 * {@see WorkOrderService} is
 * what writes the stock-ledger and general-ledger impact.
 *
 * @property int $id
 * @property string|null $naming_series
 * @property string $production_item
 * @property string|null $item_name
 * @property int|null $bom_id
 * @property float $qty
 * @property int|null $company_id
 * @property WorkOrderStatus $status
 * @property int|null $wip_warehouse_id
 * @property int|null $fg_warehouse_id
 * @property Carbon|null $planned_start_date
 * @property float $produced_qty
 * @property float $material_transferred_for_manufacturing
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Bom|null $bom
 * @property-read Collection<int, WorkOrderItem> $requiredItems
 */
class WorkOrder extends Model implements PostsToLedger, SubmittableDocument
{
    use HasCompany;
    use HasFactory;
    use HasNamingSeries;
    use IsSubmittable {
        submit as protected performSubmit;
    }

    protected $fillable = [
        'naming_series',
        'production_item',
        'item_name',
        'bom_id',
        'qty',
        'company_id',
        'status',
        'wip_warehouse_id',
        'fg_warehouse_id',
        'planned_start_date',
        'produced_qty',
        'material_transferred_for_manufacturing',
        'docstatus',
    ];

    protected $attributes = [
        'qty' => 1,
        'status' => WorkOrderStatus::Draft->value,
        'produced_qty' => 0,
        'material_transferred_for_manufacturing' => 0,
        'docstatus' => 0,
    ];

    protected $casts = [
        'qty' => 'float',
        'status' => WorkOrderStatus::class,
        'planned_start_date' => 'datetime',
        'produced_qty' => 'float',
        'material_transferred_for_manufacturing' => 'float',
        'docstatus' => DocStatus::class,
    ];

    public function getTable(): string
    {
        return (config('erp-manufacturing.table_prefix') ?? '').'work_orders';
    }

    public function bom(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::bom(), 'bom_id');
    }

    public function requiredItems(): HasMany
    {
        return $this->hasMany(ModelResolver::workOrderItem(), 'work_order_id');
    }

    public function wipWarehouse(): BelongsTo
    {
        return $this->belongsTo(StockModelResolver::warehouse(), 'wip_warehouse_id');
    }

    public function fgWarehouse(): BelongsTo
    {
        return $this->belongsTo(StockModelResolver::warehouse(), 'fg_warehouse_id');
    }

    public function submit(): void
    {
        if ($this->isDraft()) {
            $this->populateRequiredItemsFromBom();

            if ($this->status === WorkOrderStatus::Draft) {
                $this->setAttribute('status', WorkOrderStatus::NotStarted);
            }
        }

        $this->performSubmit();
    }

    /**
     * Populate the required-materials rows from the BOM, scaled to the ordered
     * quantity. Does nothing when materials have already been entered or no BOM
     * is linked.
     */
    public function populateRequiredItemsFromBom(): void
    {
        if ($this->bom_id === null || $this->requiredItems()->exists()) {
            return;
        }

        $bom = $this->bom;

        if ($bom === null) {
            return;
        }

        $bomQuantity = (float) $bom->quantity;
        $factor = $bomQuantity > 0 ? (float) $this->qty / $bomQuantity : 1.0;

        foreach ($bom->items as $item) {
            $this->requiredItems()->create([
                'item_code' => $item->item_code,
                'item_name' => $item->item_name,
                'source_warehouse_id' => $this->wip_warehouse_id,
                'required_qty' => (float) $item->qty * $factor,
                'rate' => (float) $item->rate,
            ]);
        }
    }

    public function postLedgerEntries(): void
    {
        // No-op: the Manufacture stock entry posts the GL and stock ledger.
    }

    public function reverseLedgerEntries(): void
    {
        // No-op: the Manufacture stock entry posts the GL and stock ledger.
    }
}
