<?php

namespace JeffersonGoncalves\Erp\Core\Enums;

enum AddressType: string
{
    case Billing = 'Billing';
    case Shipping = 'Shipping';
    case Office = 'Office';
    case Other = 'Other';

    public function label(): string
    {
        return __('erp-core::erp-core.address_type.'.$this->value);
    }
}
