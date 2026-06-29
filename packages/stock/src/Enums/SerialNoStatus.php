<?php

namespace JeffersonGoncalves\Erp\Stock\Enums;

enum SerialNoStatus: string
{
    case Active = 'Active';
    case Inactive = 'Inactive';
    case Delivered = 'Delivered';
    case Expired = 'Expired';

    public function label(): string
    {
        return __('erp-stock::erp-stock.serial_no_status.'.$this->value);
    }
}
