<?php

namespace JeffersonGoncalves\Erp\Selling\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Selling\Support\ModelResolver;

/**
 * @property int $id
 * @property int $quotation_id
 * @property string $item_code
 * @property string|null $item_name
 * @property string|null $description
 * @property float $qty
 * @property float $rate
 * @property float $amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Quotation|null $quotation
 */
class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'item_code',
        'item_name',
        'description',
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
        static::saving(function (QuotationItem $item): void {
            $item->amount = (float) $item->qty * (float) $item->rate;
        });

        static::saved(fn (QuotationItem $item) => $item->syncParentTotals());
        static::deleted(fn (QuotationItem $item) => $item->syncParentTotals());
    }

    public function getTable(): string
    {
        return (config('erp-selling.table_prefix') ?? '').'quotation_items';
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::quotation(), 'quotation_id');
    }

    protected function syncParentTotals(): void
    {
        $parent = $this->quotation;

        if ($parent === null || $parent->docstatus !== DocStatus::Draft) {
            return;
        }

        $parent->save();
    }
}
