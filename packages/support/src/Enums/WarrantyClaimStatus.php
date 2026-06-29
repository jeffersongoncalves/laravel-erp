<?php

namespace JeffersonGoncalves\Erp\Support\Enums;

enum WarrantyClaimStatus: string
{
    case Open = 'Open';
    case InProgress = 'In Progress';
    case Resolved = 'Resolved';
    case Closed = 'Closed';
    case Cancelled = 'Cancelled';

    public function label(): string
    {
        return __('erp-support::erp-support.warranty_claim_status.'.$this->value);
    }
}
