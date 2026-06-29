<?php

namespace JeffersonGoncalves\Erp\Selling\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Concerns\HasNamingSeries;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\PostsToLedger;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Selling\Enums\QuotationStatus;
use JeffersonGoncalves\Erp\Selling\Support\ModelResolver;

/**
 * @property int $id
 * @property string|null $naming_series
 * @property string $party_type
 * @property int|null $party_id
 * @property string $customer_name
 * @property Carbon $quotation_date
 * @property Carbon|null $valid_till
 * @property int|null $company_id
 * @property string $currency
 * @property QuotationStatus $status
 * @property float $net_total
 * @property float $grand_total
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, QuotationItem> $items
 */
class Quotation extends Model implements PostsToLedger, SubmittableDocument
{
    use HasCompany;
    use HasFactory;
    use HasNamingSeries;
    use IsSubmittable;

    protected $fillable = [
        'naming_series',
        'party_type',
        'party_id',
        'customer_name',
        'quotation_date',
        'valid_till',
        'company_id',
        'currency',
        'status',
        'net_total',
        'grand_total',
        'docstatus',
    ];

    protected $attributes = [
        'party_type' => 'Customer',
        'currency' => 'USD',
        'status' => 'Draft',
        'net_total' => 0,
        'grand_total' => 0,
        'docstatus' => 0,
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'valid_till' => 'date',
        'status' => QuotationStatus::class,
        'net_total' => 'float',
        'grand_total' => 'float',
        'docstatus' => DocStatus::class,
    ];

    protected static function booted(): void
    {
        static::saving(function (Quotation $quotation): void {
            if ($quotation->docstatus === DocStatus::Draft) {
                $quotation->calculateTotals();
            }
        });
    }

    public function getTable(): string
    {
        return (config('erp-selling.table_prefix') ?? '').'quotations';
    }

    public function items(): HasMany
    {
        return $this->hasMany(ModelResolver::quotationItem(), 'quotation_id');
    }

    public function calculateTotals(): void
    {
        $netTotal = $this->exists ? (float) $this->items()->sum('amount') : 0.0;

        $this->net_total = $netTotal;
        $this->grand_total = $netTotal;
    }

    /**
     * Quotations are commitments, not accounting documents: submitting one
     * posts nothing to the general ledger.
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
