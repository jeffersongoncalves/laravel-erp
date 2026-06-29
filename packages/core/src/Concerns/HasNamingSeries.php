<?php

namespace JeffersonGoncalves\Erp\Core\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use JeffersonGoncalves\Erp\Core\Support\ModelResolver;

/**
 * Generates a document identifier from a naming-series pattern on creation.
 *
 * Models override {@see namingSeriesPattern()} to return a pattern such as
 * `SINV-.YYYY.-`. The `.YYYY.`, `.YY.` and `.MM.` placeholders are resolved
 * against the current date, then a zero-padded counter — stored per resolved
 * series in the `naming_series` table — is appended.
 *
 * @mixin Model
 */
trait HasNamingSeries
{
    public static function bootHasNamingSeries(): void
    {
        static::creating(function (Model $model): void {
            /** @var static $model */
            $model->assignNamingSeries();
        });
    }

    /**
     * The naming-series pattern for this model. Override to enable generation.
     */
    protected function namingSeriesPattern(): ?string
    {
        return null;
    }

    protected function assignNamingSeries(): void
    {
        if (! empty($this->getAttribute('name'))) {
            return;
        }

        $pattern = $this->namingSeriesPattern();

        if ($pattern === null) {
            return;
        }

        $this->setAttribute('name', $this->generateDocumentName($pattern));
    }

    protected function generateDocumentName(string $pattern): string
    {
        $series = $this->resolveSeriesPrefix($pattern);
        $counter = $this->nextSeriesCounter($series);

        return $series.str_pad((string) $counter, 5, '0', STR_PAD_LEFT);
    }

    protected function resolveSeriesPrefix(string $pattern): string
    {
        return str_replace(
            ['.YYYY.', '.YY.', '.MM.'],
            [date('Y'), date('y'), date('m')],
            $pattern
        );
    }

    protected function nextSeriesCounter(string $series): int
    {
        $model = ModelResolver::namingSeries();

        return DB::transaction(function () use ($model, $series): int {
            /** @var Model $record */
            $record = $model::query()
                ->where('series', $series)
                ->lockForUpdate()
                ->first()
                ?? $model::query()->create(['series' => $series, 'current' => 0]);

            $current = ((int) $record->getAttribute('current')) + 1;
            $record->setAttribute('current', $current);
            $record->save();

            return $current;
        });
    }
}
