<?php

namespace JeffersonGoncalves\Erp\Buying\Enums;

enum PurchaseOrderStatus: string
{
    case Draft = 'Draft';
    case ToReceiveAndBill = 'To Receive and Bill';
    case ToReceive = 'To Receive';
    case ToBill = 'To Bill';
    case Completed = 'Completed';
    case Cancelled = 'Cancelled';
    case Closed = 'Closed';

    public function label(): string
    {
        return __('erp-buying::erp-buying.purchase_order_status.'.$this->value);
    }
}
