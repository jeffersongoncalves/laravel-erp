<?php

use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Stock\Models\DeliveryNote;
use JeffersonGoncalves\Erp\Stock\Models\PackingSlip;

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->note = DeliveryNote::factory()->create(['company_id' => $this->company->id]);
    $this->item = stockItem();
});

it('submits a packing slip, flipping the docstatus without posting any ledger', function () {
    $slip = PackingSlip::factory()->create([
        'delivery_note_id' => $this->note->id,
        'from_case_no' => 1,
        'to_case_no' => 3,
        'net_weight' => 10,
        'gross_weight' => 12,
    ]);

    $slip->items()->create([
        'item_id' => $this->item->id,
        'qty' => 5,
        'batch_no' => 'BATCH-1',
    ]);

    $slip->submit();

    expect($slip->docstatus)->toBe(DocStatus::Submitted)
        ->and($slip->isSubmitted())->toBeTrue()
        ->and($slip->items()->count())->toBe(1)
        ->and($slip->deliveryNote->id)->toBe($this->note->id);
});

it('cancels a submitted packing slip', function () {
    $slip = PackingSlip::factory()->create([
        'delivery_note_id' => $this->note->id,
    ]);

    $slip->submit();
    $slip->cancel();

    expect($slip->docstatus)->toBe(DocStatus::Cancelled)
        ->and($slip->isCancelled())->toBeTrue();
});
