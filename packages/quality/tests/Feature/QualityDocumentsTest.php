<?php

use JeffersonGoncalves\Erp\Quality\Enums\NonConformanceStatus;
use JeffersonGoncalves\Erp\Quality\Enums\QualityActionStatus;
use JeffersonGoncalves\Erp\Quality\Models\NonConformance;
use JeffersonGoncalves\Erp\Quality\Models\QualityAction;
use JeffersonGoncalves\Erp\Quality\Models\QualityProcedure;
use JeffersonGoncalves\Erp\Quality\Models\QualityReview;

it('creates a non conformance with the default status and relates a procedure', function () {
    $procedure = QualityProcedure::factory()->create();
    $nc = NonConformance::factory()->create([
        'quality_procedure_id' => $procedure->id,
    ]);

    expect($nc->status)->toBeInstanceOf(NonConformanceStatus::class)
        ->and($nc->status)->toBe(NonConformanceStatus::Open)
        ->and($nc->qualityProcedure->id)->toBe($procedure->id)
        ->and($nc->company->id)->toBe($nc->company_id);
});

it('transitions a non conformance through its statuses', function () {
    $nc = NonConformance::factory()->create();

    $nc->update([
        'status' => NonConformanceStatus::Resolved->value,
        'corrective_action' => 'Re-trained operators.',
    ]);

    expect($nc->fresh()->status)->toBe(NonConformanceStatus::Resolved)
        ->and($nc->fresh()->corrective_action)->toBe('Re-trained operators.');
});

it('creates a quality action with its resolutions', function () {
    $action = QualityAction::factory()->create();

    $action->resolutions()->create([
        'problem' => 'Recurring defect in batch',
        'resolution' => 'Adjusted machine calibration',
        'status' => 'Open',
        'responsible' => 'Jane Doe',
    ]);

    $action->refresh();

    expect($action->status)->toBeInstanceOf(QualityActionStatus::class)
        ->and($action->status)->toBe(QualityActionStatus::Open)
        ->and($action->corrective_preventive)->toBe($action->corrective_preventive)
        ->and($action->resolutions)->toHaveCount(1)
        ->and($action->resolutions->first()->problem)->toBe('Recurring defect in batch')
        ->and($action->resolutions->first()->qualityAction->id)->toBe($action->id);
});

it('completes a quality action', function () {
    $action = QualityAction::factory()->create();

    $action->update(['status' => QualityActionStatus::Completed->value]);

    expect($action->fresh()->status)->toBe(QualityActionStatus::Completed);
});

it('creates a quality review with its objectives', function () {
    $review = QualityReview::factory()->create();

    $review->objectives()->create([
        'objective' => 'Reduce defects',
        'target' => '< 1%',
        'achieved' => '0.8%',
        'status' => 'Open',
    ]);

    $review->refresh();

    expect($review->status)->toBe('Open')
        ->and($review->objectives)->toHaveCount(1)
        ->and($review->objectives->first()->objective)->toBe('Reduce defects')
        ->and($review->objectives->first()->qualityReview->id)->toBe($review->id)
        ->and($review->company->id)->toBe($review->company_id);
});
