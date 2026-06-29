<?php

namespace JeffersonGoncalves\Erp\Core\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphTo;

interface ContactContract
{
    public function contactable(): MorphTo;
}
