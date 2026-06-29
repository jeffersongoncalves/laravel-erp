<?php

namespace JeffersonGoncalves\Erp\Core\Enums;

enum DocStatus: int
{
    case Draft = 0;
    case Submitted = 1;
    case Cancelled = 2;

    public function label(): string
    {
        return __('erp-core::erp-core.doc_status.'.$this->name);
    }

    public function isEditable(): bool
    {
        return $this === self::Draft;
    }
}
