<?php

namespace JeffersonGoncalves\Erp\Stock\Contracts;

interface PostsStockLedger
{
    /**
     * The two general-ledger accounts a stock movement posts against: the
     * inventory ("Stock In Hand") account and the counter account that absorbs
     * the offsetting entry (e.g. "Stock Received But Not Billed" for a receipt
     * or "Cost of Goods Sold" for a delivery).
     *
     * @return array{stock_account_id: int|null, counter_account_id: int|null}
     */
    public function stockGlAccounts(): array;
}
