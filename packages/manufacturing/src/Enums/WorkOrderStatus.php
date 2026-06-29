<?php

namespace JeffersonGoncalves\Erp\Manufacturing\Enums;

enum WorkOrderStatus: string
{
    case Draft = 'Draft';
    case NotStarted = 'Not Started';
    case InProcess = 'In Process';
    case Completed = 'Completed';
    case Stopped = 'Stopped';
    case Cancelled = 'Cancelled';

    public function label(): string
    {
        return __('erp-manufacturing::erp-manufacturing.work_order_status.'.$this->value);
    }
}
