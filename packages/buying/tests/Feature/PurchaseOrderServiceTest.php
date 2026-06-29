<?php

use JeffersonGoncalves\Erp\Accounting\Enums\AccountType;
use JeffersonGoncalves\Erp\Accounting\Enums\RootType;
use JeffersonGoncalves\Erp\Accounting\Models\Account;
use JeffersonGoncalves\Erp\Accounting\Models\PurchaseInvoice;
use JeffersonGoncalves\Erp\Accounting\Services\GeneralLedgerService;
use JeffersonGoncalves\Erp\Buying\Models\PurchaseOrder;
use JeffersonGoncalves\Erp\Buying\Services\PurchaseOrderService;
use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Stock\Models\Item;
use JeffersonGoncalves\Erp\Stock\Models\PurchaseReceipt;
use JeffersonGoncalves\Erp\Stock\Models\StockLedgerEntry;
use JeffersonGoncalves\Erp\Stock\Models\Warehouse;

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->item = Item::factory()->create();
    $this->service = app(PurchaseOrderService::class);
});

function purchaseOrderWithItem(float $qty, float $rate, ?int $warehouseId = null): PurchaseOrder
{
    $order = PurchaseOrder::factory()->create([
        'company_id' => test()->company->id,
        'supplier_name' => 'Acme Supplies',
    ]);

    $order->items()->create([
        'item_code' => test()->item->item_code,
        'item_name' => test()->item->item_name,
        'qty' => $qty,
        'rate' => $rate,
        'warehouse_id' => $warehouseId,
    ]);

    return $order->refresh();
}

describe('createPurchaseReceipt', function () {
    beforeEach(function () {
        $this->stockAccount = Account::factory()->create(['company_id' => $this->company->id]);
        $this->srbnb = Account::factory()->create(['company_id' => $this->company->id]);
        $this->warehouse = Warehouse::factory()->create([
            'company_id' => $this->company->id,
            'account_id' => $this->stockAccount->id,
        ]);
    });

    it('drafts a stock purchase receipt with matching items', function () {
        $order = purchaseOrderWithItem(10, 5, $this->warehouse->id);

        $receipt = $this->service->createPurchaseReceipt($order);

        expect($receipt)->toBeInstanceOf(PurchaseReceipt::class)
            ->and($receipt->docstatus)->toBe(DocStatus::Draft)
            ->and($receipt->supplier_name)->toBe('Acme Supplies')
            ->and($receipt->company_id)->toBe($this->company->id)
            ->and($receipt->items)->toHaveCount(1)
            ->and($receipt->items->first()->item_id)->toBe($this->item->id)
            ->and($receipt->items->first()->qty)->toBe(10.0)
            ->and($receipt->items->first()->rate)->toBe(5.0)
            ->and($receipt->items->first()->warehouse_id)->toBe($this->warehouse->id);
    });

    it('only receipts the quantity still outstanding', function () {
        $order = purchaseOrderWithItem(10, 5, $this->warehouse->id);
        $order->items->first()->update(['received_qty' => 4]);

        $receipt = $this->service->createPurchaseReceipt($order->refresh());

        expect($receipt->items->first()->qty)->toBe(6.0);
    });

    it('posts a stock ledger entry when the receipt is submitted', function () {
        $order = purchaseOrderWithItem(10, 5, $this->warehouse->id);

        $receipt = $this->service->createPurchaseReceipt($order);
        $receipt->counterAccountId = $this->srbnb->id;
        $receipt->submit();

        $sle = StockLedgerEntry::query()->first();
        $gl = app(GeneralLedgerService::class);

        expect($sle)->not->toBeNull()
            ->and($sle->actual_qty)->toBe(10.0)
            ->and($sle->stock_value)->toBe(50.0)
            ->and($gl->accountBalance($this->stockAccount))->toBe(50.0)
            ->and($gl->accountBalance($this->srbnb))->toBe(-50.0);
    });
});

describe('createPurchaseInvoice', function () {
    beforeEach(function () {
        $this->payable = Account::factory()->ofType(RootType::Liability, AccountType::Payable)->create(['company_id' => $this->company->id]);
        $this->expense = Account::factory()->ofType(RootType::Expense, AccountType::ExpenseAccount)->create(['company_id' => $this->company->id]);
    });

    it('drafts an accounting purchase invoice with matching items', function () {
        $order = purchaseOrderWithItem(4, 25);

        $invoice = $this->service->createPurchaseInvoice($order);

        expect($invoice)->toBeInstanceOf(PurchaseInvoice::class)
            ->and($invoice->docstatus)->toBe(DocStatus::Draft)
            ->and($invoice->supplier_name)->toBe('Acme Supplies')
            ->and($invoice->credit_to_id)->toBe($this->payable->id)
            ->and($invoice->items)->toHaveCount(1)
            ->and($invoice->items->first()->item_code)->toBe($this->item->item_code)
            ->and($invoice->items->first()->qty)->toBe(4.0)
            ->and($invoice->net_total)->toBe(100.0);
    });

    it('only bills the quantity still outstanding', function () {
        $order = purchaseOrderWithItem(4, 25);
        $order->items->first()->update(['billed_qty' => 1]);

        $invoice = $this->service->createPurchaseInvoice($order->refresh());

        expect($invoice->items->first()->qty)->toBe(3.0)
            ->and($invoice->net_total)->toBe(75.0);
    });

    it('posts the general ledger when expense accounts are set and the invoice is submitted', function () {
        $order = purchaseOrderWithItem(4, 25);

        $invoice = $this->service->createPurchaseInvoice($order);
        $invoice->items->first()->update(['expense_account_id' => $this->expense->id]);
        $invoice->refresh()->submit();

        $gl = app(GeneralLedgerService::class);

        expect($invoice->docstatus)->toBe(DocStatus::Submitted)
            ->and($gl->accountBalance($this->payable))->toBe(-100.0)
            ->and($gl->accountBalance($this->expense))->toBe(100.0);
    });
});
