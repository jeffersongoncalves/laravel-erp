<?php

namespace JeffersonGoncalves\Erp\Crm\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Crm\Support\ModelResolver;

/**
 * @property int $id
 * @property int $opportunity_id
 * @property string $item_code
 * @property string|null $item_name
 * @property float $qty
 * @property float $rate
 * @property float $amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Opportunity|null $opportunity
 */
class OpportunityItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'opportunity_id',
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
        static::saving(function (OpportunityItem $item): void {
            $item->amount = (float) $item->qty * (float) $item->rate;
        });

        static::saved(fn (OpportunityItem $item) => $item->syncParentAmount());
        static::deleted(fn (OpportunityItem $item) => $item->syncParentAmount());
    }

    public function getTable(): string
    {
        return (config('erp-crm.table_prefix') ?? '').'opportunity_items';
    }

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::opportunity(), 'opportunity_id');
    }

    protected function syncParentAmount(): void
    {
        $parent = $this->opportunity;

        if ($parent === null) {
            return;
        }

        $parent->calculateAmount();
        $parent->save();
    }
}
