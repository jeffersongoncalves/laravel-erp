<?php

namespace JeffersonGoncalves\Erp\Buying\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Buying\Support\ModelResolver;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;

/**
 * @property int $id
 * @property int $supplier_quotation_id
 * @property string $item_code
 * @property string|null $item_name
 * @property float $qty
 * @property float $rate
 * @property float $amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read SupplierQuotation|null $supplierQuotation
 */
class SupplierQuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_quotation_id',
        'item_code',
        'item_name',
        'qty',
        'rate',
        'amount',
    ];

    protected $attributes = [
        'qty' => 1,
        'rate' => 0,
        'amount' => 0,
    ];

    protected $casts = [
        'qty' => 'float',
        'rate' => 'float',
        'amount' => 'float',
    ];

    protected static function booted(): void
    {
        static::saving(function (SupplierQuotationItem $item): void {
            $item->amount = (float) $item->qty * (float) $item->rate;
        });

        static::saved(fn (SupplierQuotationItem $item) => $item->syncParentTotals());
        static::deleted(fn (SupplierQuotationItem $item) => $item->syncParentTotals());
    }

    public function getTable(): string
    {
        return (config('erp-buying.table_prefix') ?? '').'supplier_quotation_items';
    }

    public function supplierQuotation(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::supplierQuotation(), 'supplier_quotation_id');
    }

    protected function syncParentTotals(): void
    {
        $parent = $this->supplierQuotation;

        if ($parent === null || $parent->docstatus !== DocStatus::Draft) {
            return;
        }

        $parent->save();
    }
}
