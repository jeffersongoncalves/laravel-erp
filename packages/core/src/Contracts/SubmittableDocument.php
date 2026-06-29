<?php

namespace JeffersonGoncalves\Erp\Core\Contracts;

interface SubmittableDocument
{
    public function submit(): void;

    public function cancel(): void;
}
