<?php

namespace JeffersonGoncalves\Erp\Core\Support;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use JeffersonGoncalves\Erp\Core\Models\Contracts\AddressContract;
use JeffersonGoncalves\Erp\Core\Models\Contracts\BrandContract;
use JeffersonGoncalves\Erp\Core\Models\Contracts\CompanyContract;
use JeffersonGoncalves\Erp\Core\Models\Contracts\ContactContract;
use JeffersonGoncalves\Erp\Core\Models\Contracts\CurrencyContract;
use JeffersonGoncalves\Erp\Core\Models\Contracts\CurrencyExchangeContract;
use JeffersonGoncalves\Erp\Core\Models\Contracts\DepartmentContract;
use JeffersonGoncalves\Erp\Core\Models\Contracts\DesignationContract;
use JeffersonGoncalves\Erp\Core\Models\Contracts\FiscalYearContract;
use JeffersonGoncalves\Erp\Core\Models\Contracts\NamingSeriesContract;
use JeffersonGoncalves\Erp\Core\Models\Contracts\TermsAndConditionsContract;
use JeffersonGoncalves\Erp\Core\Models\Contracts\UomContract;
use JeffersonGoncalves\Erp\Core\Models\Contracts\UomConversionContract;

class ModelResolver
{
    /** @var array<string, string> */
    protected static array $cache = [];

    /** @return class-string<Model&CompanyContract> */
    public static function company(): string
    {
        return static::resolve('company', CompanyContract::class);
    }

    /** @return class-string<Model&CurrencyContract> */
    public static function currency(): string
    {
        return static::resolve('currency', CurrencyContract::class);
    }

    /** @return class-string<Model&CurrencyExchangeContract> */
    public static function currencyExchange(): string
    {
        return static::resolve('currency_exchange', CurrencyExchangeContract::class);
    }

    /** @return class-string<Model&UomContract> */
    public static function uom(): string
    {
        return static::resolve('uom', UomContract::class);
    }

    /** @return class-string<Model&UomConversionContract> */
    public static function uomConversion(): string
    {
        return static::resolve('uom_conversion', UomConversionContract::class);
    }

    /** @return class-string<Model&FiscalYearContract> */
    public static function fiscalYear(): string
    {
        return static::resolve('fiscal_year', FiscalYearContract::class);
    }

    /** @return class-string<Model&DepartmentContract> */
    public static function department(): string
    {
        return static::resolve('department', DepartmentContract::class);
    }

    /** @return class-string<Model&DesignationContract> */
    public static function designation(): string
    {
        return static::resolve('designation', DesignationContract::class);
    }

    /** @return class-string<Model&BrandContract> */
    public static function brand(): string
    {
        return static::resolve('brand', BrandContract::class);
    }

    /** @return class-string<Model&TermsAndConditionsContract> */
    public static function termsAndConditions(): string
    {
        return static::resolve('terms_and_conditions', TermsAndConditionsContract::class);
    }

    /** @return class-string<Model&AddressContract> */
    public static function address(): string
    {
        return static::resolve('address', AddressContract::class);
    }

    /** @return class-string<Model&ContactContract> */
    public static function contact(): string
    {
        return static::resolve('contact', ContactContract::class);
    }

    /** @return class-string<Model&NamingSeriesContract> */
    public static function namingSeries(): string
    {
        return static::resolve('naming_series', NamingSeriesContract::class);
    }

    /**
     * @param  class-string  $contract
     * @return class-string
     *
     * @throws InvalidArgumentException
     */
    protected static function resolve(string $key, string $contract): string
    {
        if (isset(static::$cache[$key])) {
            return static::$cache[$key];
        }

        /** @var class-string|null $model */
        $model = config("erp-core.models.{$key}");

        if (! $model || ! class_exists($model)) {
            throw new InvalidArgumentException(
                "Model class for [{$key}] does not exist: {$model}"
            );
        }

        if (! is_a($model, $contract, true)) {
            throw new InvalidArgumentException(
                "Model [{$model}] must implement [{$contract}]."
            );
        }

        return static::$cache[$key] = $model;
    }

    public static function flushCache(): void
    {
        static::$cache = [];
    }
}
