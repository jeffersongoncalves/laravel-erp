<?php

namespace JeffersonGoncalves\Erp\Suite\Tests;

use JeffersonGoncalves\Erp\Suite\ErpServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ErpServiceProvider::class,
        ];
    }
}
