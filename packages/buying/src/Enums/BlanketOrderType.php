<?php

namespace JeffersonGoncalves\Erp\Buying\Enums;

enum BlanketOrderType: string
{
    case Purchasing = 'Purchasing';
    case Selling = 'Selling';

    public function label(): string
    {
        return __('erp-buying::erp-buying.blanket_order_type.'.$this->value);
    }
}
