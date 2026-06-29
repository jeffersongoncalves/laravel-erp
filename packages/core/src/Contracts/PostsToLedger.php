<?php

namespace JeffersonGoncalves\Erp\Core\Contracts;

interface PostsToLedger
{
    public function postLedgerEntries(): void;

    public function reverseLedgerEntries(): void;
}
