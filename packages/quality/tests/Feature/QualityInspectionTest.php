<?php

use JeffersonGoncalves\Erp\Quality\Enums\InspectionResult;
use JeffersonGoncalves\Erp\Quality\Enums\InspectionType;
use JeffersonGoncalves\Erp\Quality\Enums\ReadingStatus;
use JeffersonGoncalves\Erp\Quality\Models\QualityInspection;
use JeffersonGoncalves\Erp\Quality\Models\QualityInspectionTemplate;

it('creates a quality inspection with the default type and status', function () {
    $inspection = QualityInspection::factory()->create();

    expect($inspection->inspection_type)->toBeInstanceOf(InspectionType::class)
        ->and($inspection->inspection_type)->toBe(InspectionType::Incoming)
        ->and($inspection->status)->toBe(InspectionResult::Accepted)
        ->and($inspection->company->id)->toBe($inspection->company_id);
});

it('relates an inspection to its template and readings', function () {
    $template = QualityInspectionTemplate::factory()->create();
    $inspection = QualityInspection::factory()->create([
        'quality_inspection_template_id' => $template->id,
    ]);

    $inspection->readings()->create([
        'specification' => 'Length',
        'reading_value' => '10.0',
        'status' => ReadingStatus::Accepted->value,
    ]);

    $inspection->refresh();

    expect($inspection->qualityInspectionTemplate->id)->toBe($template->id)
        ->and($inspection->readings)->toHaveCount(1)
        ->and($inspection->readings->first()->status)->toBe(ReadingStatus::Accepted)
        ->and($inspection->readings->first()->qualityInspection->id)->toBe($inspection->id);
});

it('flips the overall status to rejected when a reading is rejected', function () {
    $inspection = QualityInspection::factory()->create();

    $inspection->readings()->create([
        'specification' => 'Length',
        'reading_value' => '10.0',
        'status' => ReadingStatus::Accepted->value,
    ]);

    expect($inspection->fresh()->status)->toBe(InspectionResult::Accepted);

    $inspection->readings()->create([
        'specification' => 'Width',
        'reading_value' => '99.0',
        'status' => ReadingStatus::Rejected->value,
    ]);

    expect($inspection->fresh()->status)->toBe(InspectionResult::Rejected);
});

it('returns to accepted when the rejected reading is accepted', function () {
    $inspection = QualityInspection::factory()->create();

    $reading = $inspection->readings()->create([
        'specification' => 'Length',
        'reading_value' => '99.0',
        'status' => ReadingStatus::Rejected->value,
    ]);

    expect($inspection->fresh()->status)->toBe(InspectionResult::Rejected);

    $reading->update(['status' => ReadingStatus::Accepted->value]);

    expect($inspection->fresh()->status)->toBe(InspectionResult::Accepted);
});
