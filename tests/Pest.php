<?php

declare(strict_types=1);
use JeffersonGoncalves\Erp\Core\Tests\TestCase;

/*
 * Monorepo root Pest configuration.
 *
 * Test files live under packages/<module>/tests and are discovered through the
 * <testsuite> globs in phpunit.xml.dist. Each package's TestCase is bound to its
 * own tests directory below (absolute paths, since Pest resolves a relative
 * ->in() against the root tests directory). We then load each package's
 * tests/Pest.php so their helper functions are available, exactly as they are in
 * the standalone split repositories.
 */

$packages = [
    'core' => TestCase::class,
    'accounting' => JeffersonGoncalves\Erp\Accounting\Tests\TestCase::class,
    'stock' => JeffersonGoncalves\Erp\Stock\Tests\TestCase::class,
    'selling' => JeffersonGoncalves\Erp\Selling\Tests\TestCase::class,
    'buying' => JeffersonGoncalves\Erp\Buying\Tests\TestCase::class,
    'manufacturing' => JeffersonGoncalves\Erp\Manufacturing\Tests\TestCase::class,
    'assets' => JeffersonGoncalves\Erp\Assets\Tests\TestCase::class,
    'subcontracting' => JeffersonGoncalves\Erp\Subcontracting\Tests\TestCase::class,
    'crm' => JeffersonGoncalves\Erp\Crm\Tests\TestCase::class,
    'projects' => JeffersonGoncalves\Erp\Projects\Tests\TestCase::class,
    'support' => JeffersonGoncalves\Erp\Support\Tests\TestCase::class,
    'quality' => JeffersonGoncalves\Erp\Quality\Tests\TestCase::class,
    'maintenance' => JeffersonGoncalves\Erp\Maintenance\Tests\TestCase::class,
];

foreach ($packages as $dir => $testCase) {
    uses($testCase)->in(__DIR__.'/../packages/'.$dir.'/tests');
}

// Load each package's helper functions (defined in their tests/Pest.php).
foreach (array_keys($packages) as $dir) {
    require __DIR__.'/../packages/'.$dir.'/tests/Pest.php';
}
