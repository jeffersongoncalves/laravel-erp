<?php

namespace JeffersonGoncalves\Erp\Hr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use JeffersonGoncalves\Erp\Core\Concerns\HasCompany;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Hr\Enums\PayrollFrequency;

/**
 * @property int $id
 * @property int|null $company_id
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 * @property PayrollFrequency $payroll_frequency
 * @property DocStatus $docstatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company|null $company
 */
class PayrollEntry extends Model implements SubmittableDocument
{
    use HasCompany;
    use HasFactory;
    use IsSubmittable;

    protected $fillable = [
        'company_id',
        'start_date',
        'end_date',
        'payroll_frequency',
        'docstatus',
    ];

    protected $attributes = [
        'payroll_frequency' => 'Monthly',
        'docstatus' => 0,
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'payroll_frequency' => PayrollFrequency::class,
        'docstatus' => DocStatus::class,
    ];

    public function getTable(): string
    {
        return (config('erp-hr.table_prefix') ?? '').'payroll_entries';
    }
}
