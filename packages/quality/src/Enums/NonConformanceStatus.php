<?php

namespace JeffersonGoncalves\Erp\Quality\Enums;

enum NonConformanceStatus: string
{
    case Open = 'Open';
    case InProgress = 'In Progress';
    case Resolved = 'Resolved';
    case Closed = 'Closed';
    case Cancelled = 'Cancelled';

    public function label(): string
    {
        return __('erp-quality::erp-quality.non_conformance_status.'.$this->value);
    }
}
