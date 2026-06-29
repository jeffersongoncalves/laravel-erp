<?php

namespace JeffersonGoncalves\Erp\Stock\Enums;

enum ValuationMethod: string
{
    case FIFO = 'FIFO';
    case MovingAverage = 'Moving Average';

    public function label(): string
    {
        return __('erp-stock::erp-stock.valuation_method.'.$this->value);
    }
}
