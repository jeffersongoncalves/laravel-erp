<?php

use JeffersonGoncalves\Erp\Core\Enums\DocStatus;

it('has correct integer values', function () {
    expect(DocStatus::Draft->value)->toBe(0)
        ->and(DocStatus::Submitted->value)->toBe(1)
        ->and(DocStatus::Cancelled->value)->toBe(2);
});

it('returns a label for each case', function () {
    expect(DocStatus::Draft->label())->toBeString()
        ->and(DocStatus::Submitted->label())->toBeString()
        ->and(DocStatus::Cancelled->label())->toBeString();
});

it('is editable only when draft', function () {
    expect(DocStatus::Draft->isEditable())->toBeTrue()
        ->and(DocStatus::Submitted->isEditable())->toBeFalse()
        ->and(DocStatus::Cancelled->isEditable())->toBeFalse();
});
