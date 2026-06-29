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

return [
    /*
    |--------------------------------------------------------------------------
    | Table Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix applied to all tables created by the package to avoid
    | collision with existing application tables.
    | Set to null to use table names without a prefix.
    |
    */
    'table_prefix' => 'erp_',

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | Models used by the package. Can be overridden to extend the default
    | behavior. Custom models must implement the corresponding contract
    | interface (see src/Models/Contracts/).
    |
    */
    'models' => [
        'company' => Company::class,
        'currency' => Currency::class,
        'currency_exchange' => CurrencyExchange::class,
        'uom' => Uom::class,
        'uom_conversion' => UomConversion::class,
        'fiscal_year' => FiscalYear::class,
        'department' => Department::class,
        'designation' => Designation::class,
        'brand' => Brand::class,
        'terms_and_conditions' => TermsAndConditions::class,
        'address' => Address::class,
        'contact' => Contact::class,
        'naming_series' => NamingSeries::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Naming Series
    |--------------------------------------------------------------------------
    |
    | Optional default naming-series patterns keyed by document type. Patterns
    | support the `.YYYY.`, `.YY.` and `.MM.` placeholders and are completed
    | with a zero-padded, per-series incrementing counter.
    |
    | Example: 'sales_invoice' => 'SINV-.YYYY.-'
    |
    */
    'naming_series' => [],
];
