<?php

namespace JeffersonGoncalves\Erp\Buying\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Buying\Support\ModelResolver;

/**
 * @property int $id
 * @property int $rfq_id
 * @property int $supplier_id
 * @property string $quote_status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read RequestForQuotation|null $requestForQuotation
 * @property-read Supplier|null $supplier
 */
class RequestForQuotationSupplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfq_id',
        'supplier_id',
        'quote_status',
    ];

    protected $attributes = [
        'quote_status' => 'Pending',
    ];

    public function getTable(): string
    {
        return (config('erp-buying.table_prefix') ?? '').'request_for_quotation_suppliers';
    }

    public function requestForQuotation(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::requestForQuotation(), 'rfq_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::supplier(), 'supplier_id');
    }
}
