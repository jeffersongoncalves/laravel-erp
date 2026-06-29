<?php

namespace JeffersonGoncalves\Erp\Stock\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver;

/**
 * @property int $id
 * @property string $batch_id
 * @property int $item_id
 * @property Carbon|null $expiry_date
 * @property Carbon|null $manufacturing_date
 * @property float $batch_qty
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Item|null $item
 */
class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'item_id',
        'expiry_date',
        'manufacturing_date',
        'batch_qty',
    ];

    protected $attributes = [
        'batch_qty' => 0,
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'manufacturing_date' => 'date',
        'batch_qty' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-stock.table_prefix') ?? '').'batches';
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::item(), 'item_id');
    }
}
