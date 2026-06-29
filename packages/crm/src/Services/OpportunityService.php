<?php

namespace JeffersonGoncalves\Erp\Crm\Services;

use JeffersonGoncalves\Erp\Crm\Enums\OpportunityStatus;
use JeffersonGoncalves\Erp\Crm\Models\Opportunity;
use JeffersonGoncalves\Erp\Selling\Models\Quotation;
use JeffersonGoncalves\Erp\Selling\Support\ModelResolver as SellingModelResolver;

/**
 * Converts a CRM opportunity into a draft selling quotation.
 */
class OpportunityService
{
    /**
     * Create a draft quotation from an opportunity, copying every line, and move
     * the opportunity into the Quotation stage.
     */
    public function createQuotation(Opportunity $opportunity): Quotation
    {
        $quotationClass = SellingModelResolver::quotation();

        /** @var Quotation $quotation */
        $quotation = new $quotationClass;
        $quotation->fill([
            'party_type' => $opportunity->party_type ?? 'Customer',
            'party_id' => $opportunity->party_id,
            'customer_name' => $opportunity->party_name ?? '',
            'company_id' => $opportunity->company_id,
            'quotation_date' => now(),
        ]);
        $quotation->save();

        foreach ($opportunity->items as $item) {
            $quotation->items()->create([
                'item_code' => $item->item_code,
                'item_name' => $item->item_name,
                'qty' => $item->qty,
                'rate' => $item->rate,
                'amount' => $item->amount,
            ]);
        }

        $opportunity->newQuery()
            ->whereKey($opportunity->getKey())
            ->update(['status' => OpportunityStatus::Quotation->value]);

        $opportunity->refresh();

        return $quotation->refresh();
    }
}
