<?php

namespace JeffersonGoncalves\Erp\Manufacturing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Support\ModelResolver as CoreModelResolver;
use JeffersonGoncalves\Erp\Manufacturing\Support\ModelResolver;

/**
 * @property int $id
 * @property int $bom_id
 * @property string $item_code
 * @property string|null $item_name
 * @property float $qty
 * @property float $rate
 * @property float $amount
 * @property int|null $stock_uom_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Bom|null $bom
 */
class BomItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bom_id',
        'item_code',
        'item_name',
        'qty',
        'rate',
        'amount',
        'stock_uom_id',
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
        static::saving(function (BomItem $item): void {
            $item->amount = (float) $item->qty * (float) $item->rate;
        });

        static::saved(fn (BomItem $item) => $item->recalculateBom());
        static::deleted(fn (BomItem $item) => $item->recalculateBom());
    }

    public function getTable(): string
    {
        return (config('erp-manufacturing.table_prefix') ?? '').'bom_items';
    }

    public function bom(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::bom(), 'bom_id');
    }

    public function stockUom(): BelongsTo
    {
        return $this->belongsTo(CoreModelResolver::uom(), 'stock_uom_id');
    }

    protected function recalculateBom(): void
    {
        $this->bom?->recalculateCosts();
    }
}
