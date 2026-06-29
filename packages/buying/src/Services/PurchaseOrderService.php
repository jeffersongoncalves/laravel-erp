<?php

namespace JeffersonGoncalves\Erp\Buying\Services;

use Illuminate\Database\Eloquent\Model;
use JeffersonGoncalves\Erp\Accounting\Enums\AccountType;
use JeffersonGoncalves\Erp\Accounting\Models\PurchaseInvoice;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver as AccountingModelResolver;
use JeffersonGoncalves\Erp\Buying\Models\PurchaseOrder;
use JeffersonGoncalves\Erp\Stock\Models\PurchaseReceipt;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver as StockModelResolver;

/**
 * Converts a purchase order into its downstream stock and accounting documents.
 */
class PurchaseOrderService
{
    /**
     * Draft a stock purchase receipt for the quantities still to be received.
     */
    public function createPurchaseReceipt(PurchaseOrder $purchaseOrder): PurchaseReceipt
    {
        $receiptClass = StockModelResolver::purchaseReceipt();

        /** @var PurchaseReceipt $receipt */
        $receipt = new $receiptClass;
        $receipt->fill([
            'party_type' => $purchaseOrder->party_type,
            'party_id' => $purchaseOrder->party_id,
            'supplier_name' => $purchaseOrder->supplier_name,
            'company_id' => $purchaseOrder->company_id,
            'posting_date' => today(),
        ]);
        $receipt->save();

        foreach ($purchaseOrder->items as $item) {
            $remaining = (float) $item->qty - (float) $item->received_qty;

            if ($remaining <= 0.0) {
                continue;
            }

            $receipt->items()->create([
                'item_id' => $this->resolveItemId($item->item_code),
                'qty' => $remaining,
                'rate' => $item->rate,
                'warehouse_id' => $item->warehouse_id,
            ]);
        }

        return $receipt->refresh();
    }

    /**
     * Draft an accounting purchase invoice for the quantities still to be billed.
     *
     * The credit-to (Payable) account defaults to the company's payable account
     * and the per-line expense account is left null for the UI to fill in.
     */
    public function createPurchaseInvoice(PurchaseOrder $purchaseOrder): PurchaseInvoice
    {
        $invoiceClass = AccountingModelResolver::purchaseInvoice();

        /** @var PurchaseInvoice $invoice */
        $invoice = new $invoiceClass;
        $invoice->fill([
            'party_type' => $purchaseOrder->party_type,
            'party_id' => $purchaseOrder->party_id,
            'supplier_name' => $purchaseOrder->supplier_name,
            'company_id' => $purchaseOrder->company_id,
            'currency' => $purchaseOrder->currency,
            'posting_date' => today(),
            'credit_to_id' => $this->defaultPayableAccountId($purchaseOrder->company_id),
        ]);
        $invoice->save();

        foreach ($purchaseOrder->items as $item) {
            $remaining = (float) $item->qty - (float) $item->billed_qty;

            if ($remaining <= 0.0) {
                continue;
            }

            $invoice->items()->create([
                'item_code' => $item->item_code,
                'item_name' => $item->item_name,
                'description' => $item->description,
                'qty' => $remaining,
                'rate' => $item->rate,
                'expense_account_id' => null,
            ]);
        }

        return $invoice->refresh();
    }

    protected function resolveItemId(string $itemCode): ?int
    {
        $itemClass = StockModelResolver::item();

        /** @var Model|null $item */
        $item = $itemClass::query()->where('item_code', $itemCode)->first();

        return $item === null ? null : (int) $item->getKey();
    }

    protected function defaultPayableAccountId(?int $companyId): ?int
    {
        $accountClass = AccountingModelResolver::account();

        $query = $accountClass::query()->where('account_type', AccountType::Payable->value);

        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }

        /** @var Model|null $account */
        $account = $query->first();

        return $account === null ? null : (int) $account->getKey();
    }
}
