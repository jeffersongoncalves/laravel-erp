<?php

namespace JeffersonGoncalves\Erp\Buying\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $supplier_scorecard_id
 * @property string $criteria_name
 * @property float $weight
 * @property float $max_score
 * @property float $score
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read SupplierScorecard|null $scorecard
 */
class SupplierScorecardCriteria extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_scorecard_id',
        'criteria_name',
        'weight',
        'max_score',
        'score',
    ];

    protected $attributes = [
        'weight' => 0,
        'max_score' => 100,
        'score' => 0,
    ];

    protected $casts = [
        'weight' => 'float',
        'max_score' => 'float',
        'score' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-buying.table_prefix') ?? '').'supplier_scorecard_criteria';
    }

    public function scorecard(): BelongsTo
    {
        return $this->belongsTo(SupplierScorecard::class, 'supplier_scorecard_id');
    }
}
