<?php

namespace JeffersonGoncalves\Erp\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver as AccountingModelResolver;
use JeffersonGoncalves\Erp\Hr\Enums\SalaryComponentType;

/**
 * @property int $id
 * @property string $name
 * @property SalaryComponentType $type
 * @property int|null $account_id
 * @property bool $is_taxable
 * @property float $amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Account|null $account
 */
class SalaryComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'account_id',
        'is_taxable',
        'amount',
    ];

    protected $attributes = [
        'type' => 'Earning',
        'is_taxable' => false,
        'amount' => 0,
    ];

    protected $casts = [
        'type' => SalaryComponentType::class,
        'is_taxable' => 'boolean',
        'amount' => 'float',
    ];

    public function getTable(): string
    {
        return (config('erp-hr.table_prefix') ?? '').'salary_components';
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(AccountingModelResolver::account(), 'account_id');
    }
}
