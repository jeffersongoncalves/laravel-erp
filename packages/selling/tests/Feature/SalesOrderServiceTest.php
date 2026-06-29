<?php

use JeffersonGoncalves\Erp\Accounting\Enums\AccountType;
use JeffersonGoncalves\Erp\Accounting\Enums\RootType;
use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Accounting\Models\SalesInvoice;
use JeffersonGoncalves\Erp\Accounting\Services\GeneralLedgerService;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Selling\Models\SalesOrder;
use JeffersonGoncalves\Erp\Selling\Services\SalesOrderService;
use JeffersonGoncalves\Erp\Stock\Models\DeliveryNote;
use JeffersonGoncalves\Erp\Stock\Models\Item;
use JeffersonGoncalves\Erp\Stock\Models\StockLedgerEntry;
use JeffersonGoncalves\Erp\Stock\Models\Warehouse;

it('converts a sales order into a stock delivery note and posts the stock ledger', function () {
    config()->set('erp-stock.allow_negative_stock', true);

    $company = Company::factory()->create();
    $warehouse = Warehouse::factory()->create(['company_id' => $company->id]);
    $item = Item::factory()->create(['item_code' => 'WIDGET-1']);

    $salesOrder = SalesOrder::factory()->create(['company_id' => $company->id]);
    $salesOrder->items()->create([
        'item_code' => 'WIDGET-1',
        'qty' => 5,
        'rate' => 12,
        'warehouse_id' => $warehouse->id,
    ]);
    $salesOrder->submit();

    $deliveryNote = app(SalesOrderService::class)->createDeliveryNote($salesOrder);

    expect($deliveryNote)->toBeInstanceOf(DeliveryNote::class)
        ->and($deliveryNote->exists)->toBeTrue()
        ->and($deliveryNote->customer_name)->toBe($salesOrder->customer_name)
        ->and($deliveryNote->items)->toHaveCount(1)
        ->and($deliveryNote->items->first()->item_id)->toBe($item->id)
        ->and($deliveryNote->items->first()->qty)->toBe(5.0);

    // The order line is now flagged as fully delivered.
    expect($salesOrder->items->first()->fresh()->delivered_qty)->toBe(5.0);

    // Submitting the generated delivery note drives the stock engine.
    $deliveryNote->submit();

    $entries = StockLedgerEntry::query()
        ->where('voucherable_type', $deliveryNote->getMorphClass())
        ->where('voucherable_id', $deliveryNote->getKey())
        ->get();

    expect($entries)->toHaveCount(1)
        ->and($entries->first()->actual_qty)->toBe(-5.0)
        ->and($entries->first()->item_id)->toBe($item->id);
});

it('converts a sales order into an accounting sales invoice and posts the general ledger', function () {
    $company = Company::factory()->create();
    $receivable = Account::factory()->ofType(RootType::Asset, AccountType::Receivable)->create(['company_id' => $company->id]);
    $income = Account::factory()->ofType(RootType::Income, AccountType::Income)->create(['company_id' => $company->id]);

    $salesOrder = SalesOrder::factory()->create(['company_id' => $company->id]);
    $salesOrder->items()->create(['item_code' => 'WIDGET-1', 'item_name' => 'Widget', 'qty' => 2, 'rate' => 50]);
    $salesOrder->submit();

    $invoice = app(SalesOrderService::class)->createSalesInvoice($salesOrder, $receivable->id, $income->id);

    expect($invoice)->toBeInstanceOf(SalesInvoice::class)
        ->and($invoice->exists)->toBeTrue()
        ->and($invoice->customer_name)->toBe($salesOrder->customer_name)
        ->and($invoice->items)->toHaveCount(1)
        ->and($invoice->items->first()->item_code)->toBe('WIDGET-1')
        ->and($invoice->items->first()->qty)->toBe(2.0)
        ->and($invoice->grand_total)->toBe(100.0);

    expect($salesOrder->items->first()->fresh()->billed_qty)->toBe(2.0);

    // Submitting the generated invoice posts the balanced ledger entries.
    $invoice->submit();

    $ledger = app(GeneralLedgerService::class);

    expect($ledger->accountBalance($receivable))->toBe(100.0)
        ->and($ledger->accountBalance($income))->toBe(-100.0);
});
