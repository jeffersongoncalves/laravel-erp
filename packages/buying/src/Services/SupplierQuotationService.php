<?php

namespace JeffersonGoncalves\Erp\Buying\Services;

use JeffersonGoncalves\Erp\Buying\Models\PurchaseOrder;
use JeffersonGoncalves\Erp\Buying\Models\SupplierQuotation;
use JeffersonGoncalves\Erp\Buying\Support\ModelResolver;

/**
 * Converts a supplier quotation into a purchase order.
 */
class SupplierQuotationService
{
    public function createPurchaseOrder(SupplierQuotation $supplierQuotation): PurchaseOrder
    {
        $orderClass = ModelResolver::purchaseOrder();

        /** @var PurchaseOrder $order */
        $order = new $orderClass;
        $order->fill([
            'party_type' => $supplierQuotation->party_type,
            'party_id' => $supplierQuotation->party_id,
            'supplier_name' => $supplierQuotation->supplier_name,
            'transaction_date' => today(),
            'company_id' => $supplierQuotation->company_id,
            'currency' => $supplierQuotation->currency,
        ]);
        $order->save();

        foreach ($supplierQuotation->items as $item) {
            $order->items()->create([
                'item_code' => $item->item_code,
                'item_name' => $item->item_name,
                'qty' => $item->qty,
                'rate' => $item->rate,
            ]);
        }

        return $order->refresh();
    }
}
