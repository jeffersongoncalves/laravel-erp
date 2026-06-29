<?php

namespace JeffersonGoncalves\Erp\Core\Concerns;

use DomainException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use JeffersonGoncalves\Erp\Core\Contracts\PostsToLedger;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Core\Events\DocumentCancelled;
use JeffersonGoncalves\Erp\Core\Events\DocumentSubmitted;

/**
 * Adds submit / cancel lifecycle to a document model.
 *
 * Requires a `docstatus` column cast to {@see DocStatus}. A submitted document
 * is immutable: any update (other than the controlled transition to cancelled)
 * or delete throws a {@see DomainException}.
 *
 * @mixin Model
 */
trait IsSubmittable
{
    public static function bootIsSubmittable(): void
    {
        static::updating(function (Model $model): void {
            $wasSubmitted = (int) $model->getRawOriginal('docstatus') === DocStatus::Submitted->value;

            if ($wasSubmitted && $model->getAttribute('docstatus') !== DocStatus::Cancelled) {
                throw new DomainException('Cannot modify a submitted document');
            }
        });

        static::deleting(function (Model $model): void {
            if ($model->getAttribute('docstatus') === DocStatus::Submitted) {
                throw new DomainException('Cannot modify a submitted document');
            }
        });
    }

    public function submit(): void
    {
        if (! $this->isDraft()) {
            throw new DomainException('Only a draft document can be submitted');
        }

        $this->setAttribute('docstatus', DocStatus::Submitted);
        $this->save();

        if ($this instanceof PostsToLedger) {
            $this->postLedgerEntries();
        }

        DocumentSubmitted::dispatch($this);
    }

    public function cancel(): void
    {
        if (! $this->isSubmitted()) {
            throw new DomainException('Only a submitted document can be cancelled');
        }

        $this->setAttribute('docstatus', DocStatus::Cancelled);
        $this->save();

        if ($this instanceof PostsToLedger) {
            $this->reverseLedgerEntries();
        }

        DocumentCancelled::dispatch($this);
    }

    public function isDraft(): bool
    {
        return $this->getAttribute('docstatus') === DocStatus::Draft;
    }

    public function isSubmitted(): bool
    {
        return $this->getAttribute('docstatus') === DocStatus::Submitted;
    }

    public function isCancelled(): bool
    {
        return $this->getAttribute('docstatus') === DocStatus::Cancelled;
    }

    /**
     * @param  Builder<Model>  $query
     * @return Builder<Model>
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('docstatus', DocStatus::Draft);
    }

    /**
     * @param  Builder<Model>  $query
     * @return Builder<Model>
     */
    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->where('docstatus', DocStatus::Submitted);
    }

    /**
     * @param  Builder<Model>  $query
     * @return Builder<Model>
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('docstatus', DocStatus::Cancelled);
    }
}
