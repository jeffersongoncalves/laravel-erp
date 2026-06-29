<?php

namespace JeffersonGoncalves\Erp\Selling\Enums;

enum QuotationStatus: string
{
    case Draft = 'Draft';
    case Open = 'Open';
    case Ordered = 'Ordered';
    case Lost = 'Lost';
    case Expired = 'Expired';
    case Cancelled = 'Cancelled';

    public function label(): string
    {
        return __('erp-selling::erp-selling.quotation_status.'.$this->value);
    }
}
