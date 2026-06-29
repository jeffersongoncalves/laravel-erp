<?php

namespace JeffersonGoncalves\Erp\Core\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use JeffersonGoncalves\Erp\Core\Concerns\HasNamingSeries;
use JeffersonGoncalves\Erp\Core\Concerns\IsSubmittable;
use JeffersonGoncalves\Erp\Core\Contracts\SubmittableDocument;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;

/**
 * @property string|null $name
 * @property string|null $title
 * @property DocStatus $docstatus
 */
class SubmittableTestModel extends Model implements SubmittableDocument
{
    use HasNamingSeries;
    use IsSubmittable;

    protected $table = 'erp_test_documents';

    protected $guarded = [];

    protected $attributes = [
        'docstatus' => 0,
    ];

    protected $casts = [
        'docstatus' => DocStatus::class,
    ];

    protected function namingSeriesPattern(): ?string
    {
        return 'SINV-.YYYY.-';
    }

    public static function createTable(): void
    {
        if (! Schema::hasTable('erp_test_documents')) {
            Schema::create('erp_test_documents', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('title')->nullable();
                $table->unsignedTinyInteger('docstatus')->default(0);
                $table->timestamps();
            });
        }
    }
}
