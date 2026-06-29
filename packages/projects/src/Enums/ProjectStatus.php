<?php

namespace JeffersonGoncalves\Erp\Projects\Enums;

enum ProjectStatus: string
{
    case Open = 'Open';
    case Completed = 'Completed';
    case Cancelled = 'Cancelled';

    public function label(): string
    {
        return __('erp-projects::erp-projects.project_status.'.$this->value);
    }
}
