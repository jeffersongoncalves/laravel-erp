<?php

use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Manufacturing\Models\Bom;
use JeffersonGoncalves\Erp\Manufacturing\Models\WorkOrder;
use JeffersonGoncalves\Erp\Manufacturing\Services\WorkOrderService;
use JeffersonGoncalves\Erp\Stock\Enums\StockEntryType;
use JeffersonGoncalves\Erp\Stock\Models\Item;
use JeffersonGoncalves\Erp\Stock\Models\StockEntry;
use JeffersonGoncalves\Erp\Stock\Models\StockLedgerEntry;
use JeffersonGoncalves\Erp\Stock\Models\Warehouse;

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->wip = Warehouse::factory()->create(['company_id' => $this->company->id]);
    $this->fg = Warehouse::factory()->create(['company_id' => $this->company->id]);

    $this->rawItem = Item::factory()->create(['item_code' => 'RAW-STEEL']);
    $this->finishedItem = Item::factory()->create(['item_code' => 'FG-WIDGET']);

    // Seed raw-material stock in the WIP warehouse so the production consume
    // does not overdraw the bin.
    $receipt = StockEntry::factory()->type(StockEntryType::MaterialReceipt)->create([
        'company_id' => $this->company->id,
        'to_warehouse_id' => $this->wip->id,
    ]);
    $receipt->items()->create([
        'item_id' => $this->rawItem->id,
        't_warehouse_id' => $this->wip->id,
        'qty' => 100,
        'basic_rate' => 5,
    ]);
    $receipt->refresh()->submit();

    $this->bom = Bom::factory()->create([
        'item_code' => 'FG-WIDGET',
        'quantity' => 1,
        'company_id' => $this->company->id,
    ]);
    $this->bom->items()->create(['item_code' => 'RAW-STEEL', 'qty' => 2, 'rate' => 5]);
});

it('builds a Manufacture stock entry consuming raw and producing the finished good', function () {
    $workOrder = WorkOrder::factory()->create([
        'production_item' => 'FG-WIDGET',
        'bom_id' => $this->bom->id,
        'qty' => 10,
        'company_id' => $this->company->id,
        'wip_warehouse_id' => $this->wip->id,
        'fg_warehouse_id' => $this->fg->id,
    ]);

    $workOrder->submit();

    $entry = app(WorkOrderService::class)->manufacture($workOrder->refresh());

    expect($entry->stock_entry_type)->toBe(StockEntryType::Manufacture)
        ->and($entry->docstatus)->toBe(DocStatus::Draft)
        ->and($entry->items)->toHaveCount(2);

    $rawLine = $entry->items->firstWhere('item_id', $this->rawItem->id);
    $fgLine = $entry->items->firstWhere('item_id', $this->finishedItem->id);

    expect($rawLine->s_warehouse_id)->toBe($this->wip->id)
        ->and($rawLine->qty)->toBe(20.0)
        ->and($fgLine->t_warehouse_id)->toBe($this->fg->id)
        ->and($fgLine->qty)->toBe(10.0);
});

it('writes raw-negative and finished-positive stock ledger entries when the entry is submitted', function () {
    $workOrder = WorkOrder::factory()->create([
        'production_item' => 'FG-WIDGET',
        'bom_id' => $this->bom->id,
        'qty' => 10,
        'company_id' => $this->company->id,
        'wip_warehouse_id' => $this->wip->id,
        'fg_warehouse_id' => $this->fg->id,
    ]);

    $workOrder->submit();

    $entry = app(WorkOrderService::class)->manufacture($workOrder->refresh());
    $entry->submit();

    $rawConsume = StockLedgerEntry::query()
        ->where('item_id', $this->rawItem->id)
        ->where('warehouse_id', $this->wip->id)
        ->where('actual_qty', '<', 0)
        ->first();

    $fgProduce = StockLedgerEntry::query()
        ->where('item_id', $this->finishedItem->id)
        ->where('warehouse_id', $this->fg->id)
        ->where('actual_qty', '>', 0)
        ->first();

    expect($entry->docstatus)->toBe(DocStatus::Submitted)
        ->and($rawConsume)->not->toBeNull()
        ->and($rawConsume->actual_qty)->toBe(-20.0)
        ->and($fgProduce)->not->toBeNull()
        ->and($fgProduce->actual_qty)->toBe(10.0);
});
