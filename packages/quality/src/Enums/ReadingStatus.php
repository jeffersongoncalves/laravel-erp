<?php

namespace JeffersonGoncalves\Erp\Quality\Enums;

enum ReadingStatus: string
{
    case Accepted = 'Accepted';
    case Rejected = 'Rejected';

    public function label(): string
    {
        return __('erp-quality::erp-quality.reading_status.'.$this->value);
    }
}
