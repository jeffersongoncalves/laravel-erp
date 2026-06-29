<?php

use JeffersonGoncalves\Erp\Core\Models\NamingSeries;
use JeffersonGoncalves\Erp\Core\Tests\Fixtures\SubmittableTestModel;

beforeEach(function () {
    SubmittableTestModel::createTable();
});

it('generates a name from the naming series pattern', function () {
    $doc = SubmittableTestModel::create(['title' => 'Test']);

    $year = date('Y');

    expect($doc->name)->toBe("SINV-{$year}-00001");
});

it('increments the counter on each create', function () {
    $first = SubmittableTestModel::create(['title' => 'a']);
    $second = SubmittableTestModel::create(['title' => 'b']);

    $year = date('Y');

    expect($first->name)->toBe("SINV-{$year}-00001")
        ->and($second->name)->toBe("SINV-{$year}-00002");
});

it('persists the counter in the naming series table', function () {
    SubmittableTestModel::create(['title' => 'a']);
    SubmittableTestModel::create(['title' => 'b']);

    $year = date('Y');
    $series = NamingSeries::query()->where('series', "SINV-{$year}-")->first();

    expect($series)->not->toBeNull()
        ->and($series->current)->toBe(2);
});

it('does not overwrite an explicitly provided name', function () {
    $doc = SubmittableTestModel::create(['name' => 'CUSTOM-1', 'title' => 'Test']);

    expect($doc->name)->toBe('CUSTOM-1');
});
