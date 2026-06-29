<?php

namespace JeffersonGoncalves\Erp\Manufacturing\Enums;

enum BomStatus: string
{
    case Draft = 'Draft';
    case Active = 'Active';
    case Inactive = 'Inactive';

    public function label(): string
    {
        return __('erp-manufacturing::erp-manufacturing.bom_status.'.$this->value);
    }
}
