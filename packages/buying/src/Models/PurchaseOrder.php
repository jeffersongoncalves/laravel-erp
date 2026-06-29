<?php

namespace JeffersonGoncalves\Erp\Buying\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Buying\Enums\PurchaseOrderStatus;
use JeffersonGoncalves\Erp\Buying\Support\ModelResolver;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Concerns\HasNamingSeries;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;

/**
 * A purchase order placed with a supplier. A commitment document — submittable
 * with no ledger impact; inventory and accounting follow from the downstream
 * purchase receipt and purchase invoice.
 *
 * @property int $id
 * @property string|null $naming_series
 * @property string $party_type
 * @property int|null $party_id
 * @property string $supplier_name
 * @property Carbon $transaction_date
 * @property Carbon|null $schedule_date
 * @property int|null $company_id
 * @property string $currency
 * @property PurchaseOrderStatus $status
 * @property float $per_received
 * @property float $per_billed
 * @property float $net_total
 * @property float $grand_total
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, PurchaseOrderItem> $items
 */
class PurchaseOrder extends Model implements SubmittableDocument
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
        'schedule_date',
        'company_id',
        'currency',
        'status',
        'per_received',
        'per_billed',
        'net_total',
        'grand_total',
        'docstatus',
    ];

    protected $attributes = [
        'party_type' => 'Supplier',
        'currency' => 'USD',
        'status' => PurchaseOrderStatus::Draft->value,
        'per_received' => 0,
        'per_billed' => 0,
        'net_total' => 0,
        'grand_total' => 0,
        'docstatus' => 0,
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'schedule_date' => 'date',
        'status' => PurchaseOrderStatus::class,
        'per_received' => 'float',
        'per_billed' => 'float',
        'net_total' => 'float',
        'grand_total' => 'float',
        'docstatus' => DocStatus::class,
    ];

    protected static function booted(): void
    {
        static::saving(function (PurchaseOrder $order): void {
            if ($order->docstatus === DocStatus::Draft) {
                $order->calculateTotals();
            }
        });
    }

    public function getTable(): string
    {
        return (config('erp-buying.table_prefix') ?? '').'purchase_orders';
    }

    public function items(): HasMany
    {
        return $this->hasMany(ModelResolver::purchaseOrderItem(), 'purchase_order_id');
    }

    public function calculateTotals(): void
    {
        $netTotal = $this->exists ? (float) $this->items()->sum('amount') : 0.0;

        $this->net_total = $netTotal;
        $this->grand_total = $netTotal;
    }
}
