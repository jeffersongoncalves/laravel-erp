<?php

namespace JeffersonGoncalves\Erp\Support\Enums;

enum IssueStatus: string
{
    case Open = 'Open';
    case Replied = 'Replied';
    case OnHold = 'On Hold';
    case Resolved = 'Resolved';
    case Closed = 'Closed';

    public function label(): string
    {
        return __('erp-support::erp-support.issue_status.'.$this->value);
    }
}
