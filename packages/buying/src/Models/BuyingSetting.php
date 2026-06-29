<?php

namespace JeffersonGoncalves\Erp\Buying\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Single-row table holding package-wide buying defaults.
 *
 * @property int $id
 * @property string|null $buying_price_list
 * @property bool $po_required
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class BuyingSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'buying_price_list',
        'po_required',
    ];

    protected $attributes = [
        'po_required' => false,
    ];

    protected $casts = [
        'po_required' => 'boolean',
    ];

    public function getTable(): string
    {
        return (config('erp-buying.table_prefix') ?? '').'buying_settings';
    }
}
