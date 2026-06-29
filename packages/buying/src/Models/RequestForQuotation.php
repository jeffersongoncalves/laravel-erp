<?php

namespace JeffersonGoncalves\Erp\Buying\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Buying\Enums\RfqStatus;
use JeffersonGoncalves\Erp\Buying\Support\ModelResolver;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Concerns\HasNamingSeries;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;

/**
 * A request for quotation: invites a set of suppliers to quote on a list of
 * items. A commitment document — submittable but with no ledger impact.
 *
 * @property int $id
 * @property string|null $naming_series
 * @property Carbon $transaction_date
 * @property int|null $company_id
 * @property RfqStatus $status
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, RequestForQuotationItem> $items
 * @property-read Collection<int, RequestForQuotationSupplier> $suppliers
 */
class RequestForQuotation extends Model implements SubmittableDocument
{
    use HasCompany;
    use HasFactory;
    use HasNamingSeries;
    use IsSubmittable;

    protected $fillable = [
        'naming_series',
        'transaction_date',
        'company_id',
        'status',
        'docstatus',
    ];

    protected $attributes = [
        'status' => RfqStatus::Draft->value,
        'docstatus' => 0,
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'status' => RfqStatus::class,
        'docstatus' => DocStatus::class,
    ];

    public function getTable(): string
    {
        return (config('erp-buying.table_prefix') ?? '').'request_for_quotations';
    }

    public function items(): HasMany
    {
        return $this->hasMany(ModelResolver::requestForQuotationItem(), 'rfq_id');
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(ModelResolver::requestForQuotationSupplier(), 'rfq_id');
    }
}
