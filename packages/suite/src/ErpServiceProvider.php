<?php

namespace JeffersonGoncalves\Erp\Suite;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

/**
 * Convenience meta-package. Requiring jeffersongoncalves/laravel-erp pulls the
 * full domain ecosystem (the 13 laravel-erp-* packages); each of those registers
 * its own models, migrations and config. This provider only aggregates.
 */
class ErpServiceProvider extends PackageServiceProvider
{
    public static string $name = 'laravel-erp';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name);
    }
}
