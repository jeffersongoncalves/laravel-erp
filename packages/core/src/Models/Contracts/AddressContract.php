<?php

namespace JeffersonGoncalves\Erp\Core\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphTo;

interface AddressContract
{
    public function addressable(): MorphTo;
}
