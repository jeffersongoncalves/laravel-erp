<?php

use JeffersonGoncalves\Erp\Core\Models\Address;
use JeffersonGoncalves\Erp\Core\Models\Brand;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Core\Models\Contact;
use JeffersonGoncalves\Erp\Core\Models\Currency;
use JeffersonGoncalves\Erp\Core\Models\CurrencyExchange;
use JeffersonGoncalves\Erp\Core\Models\Department;
use JeffersonGoncalves\Erp\Core\Models\Designation;
use JeffersonGoncalves\Erp\Core\Models\FiscalYear;
use JeffersonGoncalves\Erp\Core\Models\NamingSeries;
use JeffersonGoncalves\Erp\Core\Models\TermsAndConditions;
use JeffersonGoncalves\Erp\Core\Models\Uom;
use JeffersonGoncalves\Erp\Core\Models\UomConversion;
use JeffersonGoncalves\Erp\Core\Support\ModelResolver;

afterEach(function () {
    ModelResolver::flushCache();
});

it('resolves the default model classes from config', function () {
    expect(ModelResolver::company())->toBe(Company::class)
        ->and(ModelResolver::currency())->toBe(Currency::class)
        ->and(ModelResolver::currencyExchange())->toBe(CurrencyExchange::class)
        ->and(ModelResolver::uom())->toBe(Uom::class)
        ->and(ModelResolver::uomConversion())->toBe(UomConversion::class)
        ->and(ModelResolver::fiscalYear())->toBe(FiscalYear::class)
        ->and(ModelResolver::department())->toBe(Department::class)
        ->and(ModelResolver::designation())->toBe(Designation::class)
        ->and(ModelResolver::brand())->toBe(Brand::class)
        ->and(ModelResolver::termsAndConditions())->toBe(TermsAndConditions::class)
        ->and(ModelResolver::address())->toBe(Address::class)
        ->and(ModelResolver::contact())->toBe(Contact::class)
        ->and(ModelResolver::namingSeries())->toBe(NamingSeries::class);
});

it('throws when a configured model does not exist', function () {
    ModelResolver::flushCache();
    config(['erp-core.models.company' => 'App\\Models\\DoesNotExist']);

    expect(fn () => ModelResolver::company())->toThrow(InvalidArgumentException::class);
});
