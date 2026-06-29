<?php

use JeffersonGoncalves\Erp\Core\Models\Brand;

it('uses the configured table prefix', function () {
    expect((new Brand)->getTable())->toBe('erp_brands');
});

it('can be created via factory', function () {
    $brand = Brand::factory()->create(['name' => 'Acme']);

    expect($brand->name)->toBe('Acme');
});
