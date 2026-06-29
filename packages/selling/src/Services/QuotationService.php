<?php

namespace JeffersonGoncalves\Erp\Selling\Services;

use JeffersonGoncalves\Erp\Selling\Enums\QuotationStatus;
use JeffersonGoncalves\Erp\Selling\Models\Quotation;
use JeffersonGoncalves\Erp\Selling\Models\SalesOrder;
use JeffersonGoncalves\Erp\Selling\Support\ModelResolver;

/**
 * Converts an accepted quotation into a draft sales order.
 */
class QuotationService
{
    /**
     * Create a draft sales order from a quotation, copying every line, and mark
     * the quotation as Ordered.
     */
    public function createSalesOrder(Quotation $quotation): SalesOrder
    {
        $salesOrderClass = ModelResolver::salesOrder();

        /** @var SalesOrder $salesOrder */
        $salesOrder = new $salesOrderClass;
        $salesOrder->fill([
            'party_type' => $quotation->party_type,
            'party_id' => $quotation->party_id,
            'customer_name' => $quotation->customer_name,
            'company_id' => $quotation->company_id,
            'currency' => $quotation->currency,
            'order_date' => now(),
        ]);
        $salesOrder->save();

        foreach ($quotation->items as $quotationItem) {
            $salesOrder->items()->create([
                'item_code' => $quotationItem->item_code,
                'item_name' => $quotationItem->item_name,
                'description' => $quotationItem->description,
                'qty' => $quotationItem->qty,
                'rate' => $quotationItem->rate,
            ]);
        }

        // The status is a workflow field, not document data; update it directly
        // so a submitted (immutable) quotation can still be flagged Ordered.
        $quotation->newQuery()
            ->whereKey($quotation->getKey())
            ->update(['status' => QuotationStatus::Ordered->value]);

        $quotation->refresh();

        return $salesOrder->refresh();
    }
}
