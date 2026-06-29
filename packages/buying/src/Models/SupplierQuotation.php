<?php

namespace JeffersonGoncalves\Erp\Buying\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Buying\Support\ModelResolver;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Concerns\HasNamingSeries;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;

/**
 * A supplier's priced response to a request for quotation. A commitment
 * document — submittable but with no ledger impact.
 *
 * @property int $id
 * @property string|null $naming_series
 * @property string $party_type
 * @property int|null $party_id
 * @property string $supplier_name
 * @property Carbon $transaction_date
 * @property Carbon|null $valid_till
 * @property int|null $company_id
 * @property string $currency
 * @property float $net_total
 * @property float $grand_total
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, SupplierQuotationItem> $items
 */
class SupplierQuotation extends Model implements SubmittableDocument
{
    use HasCompany;
    use HasFactory;
    use HasNamingSeries;
    use IsSubmittable;

    protected $fillable = [
        'naming_series',
        'party_type',
        'party_id',
        'supplier_name',
        'transaction_date',
        'valid_till',
        'company_id',
        'currency',
        'net_total',
        'grand_total',
        'docstatus',
    ];

    protected $attributes = [
        'party_type' => 'Supplier',
        'currency' => 'USD',
        'net_total' => 0,
        'grand_total' => 0,
        'docstatus' => 0,
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'valid_till' => 'date',
        'net_total' => 'float',
        'grand_total' => 'float',
        'docstatus' => DocStatus::class,
    ];

    protected static function booted(): void
    {
        static::saving(function (SupplierQuotation $quotation): void {
            if ($quotation->docstatus === DocStatus::Draft) {
                $quotation->calculateTotals();
            }
        });
    }

    public function getTable(): string
    {
        return (config('erp-buying.table_prefix') ?? '').'supplier_quotations';
    }

    public function items(): HasMany
    {
        return $this->hasMany(ModelResolver::supplierQuotationItem(), 'supplier_quotation_id');
    }

    public function calculateTotals(): void
    {
        $netTotal = $this->exists ? (float) $this->items()->sum('amount') : 0.0;

        $this->net_total = $netTotal;
        $this->grand_total = $netTotal;
    }
}
