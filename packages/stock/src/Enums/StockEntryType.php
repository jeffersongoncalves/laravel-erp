<?php

namespace JeffersonGoncalves\Erp\Stock\Enums;

enum StockEntryType: string
{
    case MaterialIssue = 'Material Issue';
    case MaterialReceipt = 'Material Receipt';
    case MaterialTransfer = 'Material Transfer';
    case Manufacture = 'Manufacture';
    case Repack = 'Repack';
    case SendToSubcontractor = 'Send to Subcontractor';

    public function label(): string
    {
        return __('erp-stock::erp-stock.stock_entry_type.'.$this->value);
    }
}
