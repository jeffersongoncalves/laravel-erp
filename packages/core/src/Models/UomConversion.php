<?php

namespace JeffersonGoncalves\Erp\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Models\Contracts\UomConversionContract;
use JeffersonGoncalves\Erp\Core\Support\ModelResolver;

/**
 * @property int $id
 * @property int $from_uom_id
 * @property int $to_uom_id
 * @property string $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Uom $fromUom
 * @property-read Uom $toUom
 */
class UomConversion extends Model implements UomConversionContract
{
    use HasFactory;

    protected $fillable = [
        'from_uom_id',
        'to_uom_id',
        'value',
    ];

    protected $casts = [
        'value' => 'decimal:9',
    ];

    public function getTable(): string
    {
        return (config('erp-core.table_prefix') ?? '').'uom_conversions';
    }

    public function fromUom(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::uom(), 'from_uom_id');
    }

    public function toUom(): BelongsTo
    {
        return $this->belongsTo(ModelResolver::uom(), 'to_uom_id');
    }
}
