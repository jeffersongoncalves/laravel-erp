<?php

use JeffersonGoncalves\Erp\Suite\ErpServiceProvider;

it('registers the erp suite service provider', function () {
    expect(array_key_exists(ErpServiceProvider::class, app()->getLoadedProviders()))->toBeTrue();
});
