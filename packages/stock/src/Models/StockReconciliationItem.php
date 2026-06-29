<?php

namespace JeffersonGoncalves\Erp\Stock\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver;

/**
 * @property int $id
 * @property int $stock_reconciliation_id
 * @property int $item_id
 * @property int $warehouse_id
 * @property float $qty
 * @property float $valuation_rate
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read StockReconciliation|null $stockReconciliation
 * @property-read Item|null $item
 */
class StockReconciliationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_reconciliation_id',
        'item_id',
        'warehouse_id',
        'qty',
        'valuation_rate',
    ];

    protected $attributes = [
        'qty' => 0,
        'valuation_rate' => 0,
    ];

    protected $casts = [
        'qty' => 'float',
        'valuation_rate' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-stock.table_prefix') ?? '').'stock_reconciliation_items';
    }

    public function stockReconciliation(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::stockReconciliation(), 'stock_reconciliation_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::item(), 'item_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::warehouse(), 'warehouse_id');
    }
}
