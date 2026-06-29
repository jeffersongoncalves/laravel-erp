<?php

namespace JeffersonGoncalves\Erp\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Models\Contracts\CurrencyExchangeContract;

/**
 * @property int $id
 * @property string $from_currency
 * @property string $to_currency
 * @property string $exchange_rate
 * @property Carbon $date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class CurrencyExchange extends Model implements CurrencyExchangeContract
{
    use HasFactory;

    protected $fillable = [
        'from_currency',
        'to_currency',
        'exchange_rate',
        'date',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:9',
        'date' => 'date',
    ];

    public function getTable(): string
    {
        return (config('erp-core.table_prefix') ?? '').'currency_exchanges';
    }
}
