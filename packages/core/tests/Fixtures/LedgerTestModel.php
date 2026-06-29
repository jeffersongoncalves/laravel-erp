<?php

namespace JeffersonGoncalves\Erp\Core\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\PostsToLedger;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;

/**
 * @property DocStatus $docstatus
 */
class LedgerTestModel extends Model implements PostsToLedger, SubmittableDocument
{
    use IsSubmittable;

    public bool $posted = false;

    public bool $reversed = false;

    protected $table = 'erp_ledger_documents';

    protected $guarded = [];

    protected $attributes = [
        'docstatus' => 0,
    ];

    protected $casts = [
        'docstatus' => DocStatus::class,
    ];

    public function postLedgerEntries(): void
    {
        $this->posted = true;
    }

    public function reverseLedgerEntries(): void
    {
        $this->reversed = true;
    }

    public static function createTable(): void
    {
        if (! Schema::hasTable('erp_ledger_documents')) {
            Schema::create('erp_ledger_documents', function (Blueprint $table) {
                $table->id();
                $table->unsignedTinyInteger('docstatus')->default(0);
                $table->timestamps();
            });
        }
    }
}
