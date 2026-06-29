<?php

namespace JeffersonGoncalves\Erp\Buying\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Buying\Support\ModelResolver;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver as StockModelResolver;

/**
 * @property int $id
 * @property int $rfq_id
 * @property string $item_code
 * @property string|null $item_name
 * @property float $qty
 * @property int|null $warehouse_id
 * @property Carbon|null $schedule_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read RequestForQuotation|null $requestForQuotation
 */
class RequestForQuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfq_id',
        'item_code',
        'item_name',
        'qty',
        'warehouse_id',
        'schedule_date',
    ];

    protected $attributes = [
        'qty' => 1,
    ];

    protected $casts = [
        'qty' => 'float',
        'schedule_date' => 'date',
    ];

    public function getTable(): string
    {
        return (config('erp-buying.table_prefix') ?? '').'request_for_quotation_items';
    }

    public function requestForQuotation(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::requestForQuotation(), 'rfq_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(StockModelResolver::warehouse(), 'warehouse_id');
    }
}
