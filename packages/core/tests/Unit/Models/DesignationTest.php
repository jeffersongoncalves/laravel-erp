<?php

use JeffersonGoncalves\Erp\Core\Models\Designation;

it('uses the configured table prefix', function () {
    expect((new Designation)->getTable())->toBe('erp_designations');
});

it('can be created via factory', function () {
    $designation = Designation::factory()->create(['name' => 'Engineer']);

    expect($designation->name)->toBe('Engineer');
});
