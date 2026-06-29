<?php

namespace JeffersonGoncalves\Erp\Stock\Models;

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
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver;

/**
 * A request for material. A request never moves stock or money, so its ledger
 * hooks are intentionally no-ops.
 *
 * @property int $id
 * @property string|null $naming_series
 * @property string $material_request_type
 * @property Carbon $transaction_date
 * @property int|null $company_id
 * @property string $status
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, MaterialRequestItem> $items
 */
class MaterialRequest extends Model implements PostsToLedger, SubmittableDocument
{
    use HasCompany;
    use HasFactory;
    use HasNamingSeries;
    use IsSubmittable;

    protected $fillable = [
        'naming_series',
        'material_request_type',
        'transaction_date',
        'company_id',
        'status',
        'docstatus',
    ];

    protected $attributes = [
        'material_request_type' => 'Purchase',
        'status' => 'Draft',
        'docstatus' => 0,
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'docstatus' => DocStatus::class,
    ];

    public function getTable(): string
    {
        return (config('erp-stock.table_prefix') ?? '').'material_requests';
    }

    public function items(): HasMany
    {
        return $this->hasMany(ModelResolver::materialRequestItem(), 'material_request_id');
    }

    public function postLedgerEntries(): void
    {
        // A material request produces no stock or general-ledger movement.
    }

    public function reverseLedgerEntries(): void
    {
        // A material request produces no stock or general-ledger movement.
    }
}
