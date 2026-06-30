<?php

namespace JeffersonGoncalves\Erp\Hr\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface EmployeeContract
{
    public function company(): BelongsTo;

    public function department(): BelongsTo;

    public function designation(): BelongsTo;

    public function salaryStructureAssignments(): HasMany;
}
