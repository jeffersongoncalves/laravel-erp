<?php

use JeffersonGoncalves\Erp\Quality\Models\QualityGoal;
use JeffersonGoncalves\Erp\Quality\Models\QualityInspectionTemplate;
use JeffersonGoncalves\Erp\Quality\Models\QualityProcedure;

it('creates a quality goal with its objectives', function () {
    $goal = QualityGoal::factory()->create();

    $goal->objectives()->create([
        'objective' => 'Reduce defects',
        'target' => '< 1%',
        'uom' => 'Percent',
    ]);
    $goal->objectives()->create([
        'objective' => 'Improve on-time delivery',
        'target' => '> 98%',
    ]);

    $goal->refresh();

    expect($goal->goal)->not->toBeEmpty()
        ->and($goal->frequency)->not->toBeEmpty()
        ->and($goal->objectives)->toHaveCount(2)
        ->and($goal->objectives->first()->objective)->toBe('Reduce defects')
        ->and($goal->objectives->first()->qualityGoal->id)->toBe($goal->id);
});

it('defaults a quality goal frequency to none', function () {
    $goal = QualityGoal::factory()->create(['frequency' => 'None']);

    expect($goal->frequency)->toBe('None');
});

it('creates a quality procedure tree', function () {
    $parent = QualityProcedure::factory()->group()->create();
    $child = QualityProcedure::factory()->create([
        'parent_quality_procedure_id' => $parent->id,
    ]);

    $parent->processes()->create([
        'process_description' => 'Inspect incoming goods',
        'role' => 'Quality Inspector',
    ]);

    $parent->refresh();

    expect($parent->is_group)->toBeTrue()
        ->and($child->is_group)->toBeFalse()
        ->and($child->parent->id)->toBe($parent->id)
        ->and($parent->children->pluck('id'))->toContain($child->id)
        ->and($parent->processes)->toHaveCount(1)
        ->and($parent->processes->first()->process_description)->toBe('Inspect incoming goods')
        ->and($parent->processes->first()->qualityProcedure->id)->toBe($parent->id);
});

it('creates a quality inspection template with its parameters', function () {
    $template = QualityInspectionTemplate::factory()->create();

    $template->parameters()->create([
        'specification' => 'Length',
        'numeric' => true,
        'min_value' => 9.5,
        'max_value' => 10.5,
    ]);
    $template->parameters()->create([
        'specification' => 'Colour',
        'value' => 'Blue',
    ]);

    $template->refresh();

    expect($template->name)->not->toBeEmpty()
        ->and($template->parameters)->toHaveCount(2)
        ->and($template->parameters->first()->specification)->toBe('Length')
        ->and($template->parameters->first()->numeric)->toBeTrue()
        ->and($template->parameters->first()->qualityInspectionTemplate->id)->toBe($template->id)
        ->and($template->parameters->last()->numeric)->toBeFalse();
});
