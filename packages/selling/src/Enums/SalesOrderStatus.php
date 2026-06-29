<?php

namespace JeffersonGoncalves\Erp\Selling\Enums;

enum SalesOrderStatus: string
{
    case Draft = 'Draft';
    case ToDeliverAndBill = 'To Deliver and Bill';
    case ToDeliver = 'To Deliver';
    case ToBill = 'To Bill';
    case Completed = 'Completed';
    case Cancelled = 'Cancelled';
    case Closed = 'Closed';

    public function label(): string
    {
        return __('erp-selling::erp-selling.sales_order_status.'.$this->value);
    }
}
