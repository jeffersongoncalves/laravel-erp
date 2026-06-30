<?php

use JeffersonGoncalves\Erp\Buying\Models\Supplier;
use JeffersonGoncalves\Erp\Buying\Models\SupplierScorecard;
use JeffersonGoncalves\Erp\Buying\Services\SupplierScorecardService;

beforeEach(function () {
    $this->service = app(SupplierScorecardService::class);
});

function scorecardWithCriteria(array $criteria): SupplierScorecard
{
    $card = SupplierScorecard::factory()->create([
        'supplier_id' => Supplier::factory(),
    ]);

    foreach ($criteria as $row) {
        $card->criteria()->create($row);
    }

    return $card->refresh();
}

it('computes the weighted score across criteria', function () {
    // (90/100)*60 + (50/100)*40 = 54 + 20 = 74
    $card = scorecardWithCriteria([
        ['criteria_name' => 'Quality', 'weight' => 60, 'max_score' => 100, 'score' => 90],
        ['criteria_name' => 'Delivery', 'weight' => 40, 'max_score' => 100, 'score' => 50],
    ]);

    expect($this->service->computeScore($card))->toBe(74.0);
});

it('honours non-100 max scores', function () {
    // (4/5)*100 = 80
    $card = scorecardWithCriteria([
        ['criteria_name' => 'Rating', 'weight' => 100, 'max_score' => 5, 'score' => 4],
    ]);

    expect($this->service->computeScore($card))->toBe(80.0);
});

it('ignores criteria with a zero max score', function () {
    $card = scorecardWithCriteria([
        ['criteria_name' => 'Broken', 'weight' => 100, 'max_score' => 0, 'score' => 10],
    ]);

    expect($this->service->computeScore($card))->toBe(0.0);
});

it('grades scores at the standing boundaries', function () {
    expect($this->service->grade(90))->toBe('A')
        ->and($this->service->grade(89.99))->toBe('B')
        ->and($this->service->grade(75))->toBe('B')
        ->and($this->service->grade(74.99))->toBe('C')
        ->and($this->service->grade(50))->toBe('C')
        ->and($this->service->grade(49.99))->toBe('D')
        ->and($this->service->grade(0))->toBe('D');
});

it('refreshes the persisted score and standing', function () {
    $card = scorecardWithCriteria([
        ['criteria_name' => 'Quality', 'weight' => 60, 'max_score' => 100, 'score' => 90],
        ['criteria_name' => 'Delivery', 'weight' => 40, 'max_score' => 100, 'score' => 50],
    ]);

    $this->service->refresh($card);

    expect($card->score)->toBe(74.0)
        ->and($card->standing)->toBe('C')
        ->and($card->fresh()->score)->toBe(74.0)
        ->and($card->fresh()->standing)->toBe('C');
});
