<?php

namespace JeffersonGoncalves\Erp\Selling\Services;

use JeffersonGoncalves\Erp\Accounting\Models\SalesInvoice;
use JeffersonGoncalves\Erp\Accounting\Support\ModelResolver as AccountingModelResolver;
use JeffersonGoncalves\Erp\Selling\Models\SalesOrder;
use JeffersonGoncalves\Erp\Stock\Models\DeliveryNote;
use JeffersonGoncalves\Erp\Stock\Support\ModelResolver as StockModelResolver;

/**
 * Converts a submitted sales order into the downstream stock and accounting
 * documents (delivery note, sales invoice). Both target models are resolved
 * through their owning package's ModelResolver so they stay swappable.
 */
class SalesOrderService
{
    /**
     * Build a draft delivery note from a sales order, copying the party and
     * one line per still-to-deliver order item (item resolved by item_code).
     * The caller sets the COGS counter account and submits to post the SLE/GL.
     */
    public function createDeliveryNote(SalesOrder $salesOrder): DeliveryNote
    {
        $deliveryNoteClass = StockModelResolver::deliveryNote();
        $itemClass = StockModelResolver::item();

        /** @var DeliveryNote $deliveryNote */
        $deliveryNote = new $deliveryNoteClass;
        $deliveryNote->fill([
            'party_type' => $salesOrder->party_type,
            'party_id' => $salesOrder->party_id,
            'customer_name' => $salesOrder->customer_name,
            'company_id' => $salesOrder->company_id,
            'posting_date' => now(),
        ]);
        $deliveryNote->save();

        foreach ($salesOrder->items as $orderItem) {
            $remainingQty = (float) $orderItem->qty - (float) $orderItem->delivered_qty;

            if ($remainingQty <= 0 || $orderItem->warehouse_id === null) {
                continue;
            }

            $itemId = $itemClass::query()
                ->where('item_code', $orderItem->item_code)
                ->value('id');

            if ($itemId === null) {
                continue;
            }

            $deliveryNote->items()->create([
                'item_id' => (int) $itemId,
                'qty' => $remainingQty,
                'rate' => $orderItem->rate,
                'warehouse_id' => $orderItem->warehouse_id,
                'against_sales_order' => $salesOrder->naming_series,
            ]);

            $orderItem->delivered_qty = (float) $orderItem->delivered_qty + $remainingQty;
            $orderItem->save();
        }

        return $deliveryNote->refresh();
    }

    /**
     * Build a draft sales invoice from a sales order, copying the party and one
     * line per still-to-bill order item. The receivable (debit_to) and per-line
     * income accounts are supplied by the caller/UI; the invoice is saved as a
     * draft and submitted separately to post the general-ledger entries.
     */
    public function createSalesInvoice(SalesOrder $salesOrder, ?int $debitToId = null, ?int $incomeAccountId = null): SalesInvoice
    {
        $salesInvoiceClass = AccountingModelResolver::salesInvoice();

        /** @var SalesInvoice $salesInvoice */
        $salesInvoice = new $salesInvoiceClass;
        $salesInvoice->fill([
            'party_type' => $salesOrder->party_type,
            'party_id' => $salesOrder->party_id,
            'customer_name' => $salesOrder->customer_name,
            'company_id' => $salesOrder->company_id,
            'currency' => $salesOrder->currency,
            'posting_date' => now(),
            'debit_to_id' => $debitToId,
        ]);
        $salesInvoice->save();

        foreach ($salesOrder->items as $orderItem) {
            $remainingQty = (float) $orderItem->qty - (float) $orderItem->billed_qty;

            if ($remainingQty <= 0) {
                continue;
            }

            $salesInvoice->items()->create([
                'item_code' => $orderItem->item_code,
                'item_name' => $orderItem->item_name,
                'description' => $orderItem->description,
                'qty' => $remainingQty,
                'rate' => $orderItem->rate,
                'income_account_id' => $incomeAccountId,
            ]);

            $orderItem->billed_qty = (float) $orderItem->billed_qty + $remainingQty;
            $orderItem->save();
        }

        return $salesInvoice->refresh();
    }
}
