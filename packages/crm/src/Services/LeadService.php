<?php

namespace JeffersonGoncalves\Erp\Crm\Services;

use JeffersonGoncalves\Erp\Crm\Enums\LeadStatus;
use JeffersonGoncalves\Erp\Crm\Models\Lead;
use JeffersonGoncalves\Erp\Crm\Models\Opportunity;
use JeffersonGoncalves\Erp\Crm\Support\ModelResolver;
use JeffersonGoncalves\Erp\Selling\Models\Customer;
use JeffersonGoncalves\Erp\Selling\Support\ModelResolver as SellingModelResolver;

/**
 * Converts CRM leads into the downstream selling customer and CRM opportunities.
 */
class LeadService
{
    /**
     * Promote a lead to a selling customer, flag the lead Converted and link it
     * back to the new customer.
     */
    public function createCustomer(Lead $lead): Customer
    {
        $customerClass = SellingModelResolver::customer();

        /** @var Customer $customer */
        $customer = new $customerClass;
        $customer->fill([
            'customer_name' => $lead->company_name ?: $lead->lead_name,
            'customer_group_id' => config('erp-crm.default_customer_group'),
            'default_currency' => config('erp-crm.default_currency', 'USD'),
        ]);
        $customer->save();

        // The status/customer_id are workflow fields; update them directly so a
        // lead is flagged Converted without firing the model save pipeline.
        $lead->newQuery()
            ->whereKey($lead->getKey())
            ->update([
                'customer_id' => $customer->getKey(),
                'status' => LeadStatus::Converted->value,
            ]);

        $lead->refresh();

        return $customer;
    }

    /**
     * Open an opportunity from a lead and move the lead into the Opportunity
     * stage.
     */
    public function createOpportunity(Lead $lead): Opportunity
    {
        $opportunityClass = ModelResolver::opportunity();

        /** @var Opportunity $opportunity */
        $opportunity = new $opportunityClass;
        $opportunity->fill([
            'opportunity_from' => 'Lead',
            'lead_id' => $lead->getKey(),
            'party_name' => $lead->company_name ?: $lead->lead_name,
            'campaign_id' => $lead->campaign_id,
            'company_id' => $lead->company_id,
        ]);
        $opportunity->save();

        $lead->newQuery()
            ->whereKey($lead->getKey())
            ->update(['status' => LeadStatus::Opportunity->value]);

        $lead->refresh();

        return $opportunity->refresh();
    }
}
