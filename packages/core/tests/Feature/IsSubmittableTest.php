<?php

use Illuminate\Support\Facades\Event;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Core\Events\DocumentCancelled;
use JeffersonGoncalves\Erp\Core\Events\DocumentSubmitted;
use JeffersonGoncalves\Erp\Core\Tests\Fixtures\LedgerTestModel;
use JeffersonGoncalves\Erp\Core\Tests\Fixtures\SubmittableTestModel;

beforeEach(function () {
    SubmittableTestModel::createTable();
    LedgerTestModel::createTable();
});

it('starts as a draft', function () {
    $doc = SubmittableTestModel::create(['title' => 'Test']);

    expect($doc->docstatus)->toBe(DocStatus::Draft)
        ->and($doc->isDraft())->toBeTrue();
});

it('submits a draft document', function () {
    $doc = SubmittableTestModel::create(['title' => 'Test']);

    $doc->submit();

    expect($doc->docstatus)->toBe(DocStatus::Submitted)
        ->and($doc->isSubmitted())->toBeTrue();
});

it('cannot submit a document that is not a draft', function () {
    $doc = SubmittableTestModel::create(['title' => 'Test']);
    $doc->submit();

    expect(fn () => $doc->submit())->toThrow(DomainException::class);
});

it('blocks updating a submitted document', function () {
    $doc = SubmittableTestModel::create(['title' => 'Test']);
    $doc->submit();

    $doc->title = 'Changed';

    expect(fn () => $doc->save())
        ->toThrow(DomainException::class, 'Cannot modify a submitted document');
});

it('blocks deleting a submitted document', function () {
    $doc = SubmittableTestModel::create(['title' => 'Test']);
    $doc->submit();

    expect(fn () => $doc->delete())
        ->toThrow(DomainException::class, 'Cannot modify a submitted document');
});

it('fires DocumentSubmitted on submit', function () {
    Event::fake([DocumentSubmitted::class]);

    $doc = SubmittableTestModel::create(['title' => 'Test']);
    $doc->submit();

    Event::assertDispatched(DocumentSubmitted::class);
});

it('cancels a submitted document and fires DocumentCancelled', function () {
    Event::fake([DocumentCancelled::class]);

    $doc = SubmittableTestModel::create(['title' => 'Test']);
    $doc->submit();
    $doc->cancel();

    expect($doc->docstatus)->toBe(DocStatus::Cancelled)
        ->and($doc->isCancelled())->toBeTrue();

    Event::assertDispatched(DocumentCancelled::class);
});

it('cannot cancel a document that is not submitted', function () {
    $doc = SubmittableTestModel::create(['title' => 'Test']);

    expect(fn () => $doc->cancel())->toThrow(DomainException::class);
});

it('calls ledger hooks on submit and cancel', function () {
    $doc = LedgerTestModel::create();

    $doc->submit();
    expect($doc->posted)->toBeTrue();

    $doc->cancel();
    expect($doc->reversed)->toBeTrue();
});

it('scopes documents by docstatus', function () {
    SubmittableTestModel::create(['title' => 'a']);
    $submitted = SubmittableTestModel::create(['title' => 'b']);
    $submitted->submit();

    expect(SubmittableTestModel::draft()->count())->toBe(1)
        ->and(SubmittableTestModel::submitted()->count())->toBe(1)
        ->and(SubmittableTestModel::cancelled()->count())->toBe(0);
});
