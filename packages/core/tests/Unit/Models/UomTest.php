<?php

use JeffersonGoncalves\Erp\Core\Models\Uom;
use JeffersonGoncalves\Erp\Core\Models\UomConversion;

it('uses the configured table prefix', function () {
    expect((new Uom)->getTable())->toBe('erp_uoms');
});

it('can be created via factory', function () {
    $uom = Uom::factory()->create(['name' => 'Kilogram']);

    expect($uom->name)->toBe('Kilogram')
        ->and($uom->must_be_whole_number)->toBeFalse();
});

it('scopes enabled units', function () {
    Uom::factory()->create();
    Uom::factory()->state(['enabled' => false])->create();

    expect(Uom::enabled()->count())->toBe(1);
});

it('resolves conversion relationships', function () {
    $from = Uom::factory()->create();
    $to = Uom::factory()->create();

    $conversion = UomConversion::create([
        'from_uom_id' => $from->id,
        'to_uom_id' => $to->id,
        'value' => '1.5',
    ]);

    expect($conversion->fromUom->id)->toBe($from->id)
        ->and($conversion->toUom->id)->toBe($to->id)
        ->and($conversion->getTable())->toBe('erp_uom_conversions');
});
