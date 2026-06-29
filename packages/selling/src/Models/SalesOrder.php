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
use JeffersonGoncalves\Erp\Selling\Enums\SalesOrderStatus;
use JeffersonGoncalves\Erp\Selling\Support\ModelResolver;

/**
 * @property int $id
 * @property string|null $naming_series
 * @property string $party_type
 * @property int|null $party_id
 * @property string $customer_name
 * @property Carbon $order_date
 * @property Carbon|null $delivery_date
 * @property int|null $company_id
 * @property string $currency
 * @property SalesOrderStatus $status
 * @property float $per_delivered
 * @property float $per_billed
 * @property float $net_total
 * @property float $grand_total
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, SalesOrderItem> $items
 */
class SalesOrder extends Model implements PostsToLedger, SubmittableDocument
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
        'order_date',
        'delivery_date',
        'company_id',
        'currency',
        'status',
        'per_delivered',
        'per_billed',
        'net_total',
        'grand_total',
        'docstatus',
    ];

    protected $attributes = [
        'party_type' => 'Customer',
        'currency' => 'USD',
        'status' => 'Draft',
        'per_delivered' => 0,
        'per_billed' => 0,
        'net_total' => 0,
        'grand_total' => 0,
        'docstatus' => 0,
    ];

    protected $casts = [
        'order_date' => 'date',
        'delivery_date' => 'date',
        'status' => SalesOrderStatus::class,
        'per_delivered' => 'float',
        'per_billed' => 'float',
        'net_total' => 'float',
        'grand_total' => 'float',
        'docstatus' => DocStatus::class,
    ];

    protected static function booted(): void
    {
        static::saving(function (SalesOrder $order): void {
            if ($order->docstatus === DocStatus::Draft) {
                $order->calculateTotals();
            }
        });
    }

    public function getTable(): string
    {
        return (config('erp-selling.table_prefix') ?? '').'sales_orders';
    }

    public function items(): HasMany
    {
        return $this->hasMany(ModelResolver::salesOrderItem(), 'sales_order_id');
    }

    public function calculateTotals(): void
    {
        $netTotal = $this->exists ? (float) $this->items()->sum('amount') : 0.0;

        $this->net_total = $netTotal;
        $this->grand_total = $netTotal;
    }

    /**
     * Sales orders are commitments, not accounting documents: submitting one
     * posts nothing to the general ledger. Stock and ledger impact happen when
     * the order is later converted to a delivery note or sales invoice.
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
