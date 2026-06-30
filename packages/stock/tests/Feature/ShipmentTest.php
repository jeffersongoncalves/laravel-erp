<?php

use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Stock\Models\DeliveryNote;
use JeffersonGoncalves\Erp\Stock\Models\Shipment;

beforeEach(function () {
    $this->company = Company::factory()->create();
});

it('submits a shipment, flipping the docstatus without posting any ledger', function () {
    $shipment = Shipment::factory()->create([
        'company_id' => $this->company->id,
    ]);

    $shipment->parcels()->create([
        'weight' => 12.5,
        'count' => 2,
    ]);

    $note = DeliveryNote::factory()->create(['company_id' => $this->company->id]);
    $shipment->deliveryNotes()->create(['delivery_note_id' => $note->id]);

    $shipment->submit();

    expect($shipment->docstatus)->toBe(DocStatus::Submitted)
        ->and($shipment->isSubmitted())->toBeTrue()
        ->and($shipment->parcels()->count())->toBe(1)
        ->and($shipment->deliveryNotes()->count())->toBe(1);
});

it('cancels a submitted shipment', function () {
    $shipment = Shipment::factory()->create([
        'company_id' => $this->company->id,
    ]);

    $shipment->submit();
    $shipment->cancel();

    expect($shipment->docstatus)->toBe(DocStatus::Cancelled)
        ->and($shipment->isCancelled())->toBeTrue();
});

it('forbids modifying a submitted shipment', function () {
    $shipment = Shipment::factory()->create([
        'company_id' => $this->company->id,
    ]);

    $shipment->submit();

    expect(fn () => $shipment->update(['value_of_goods' => 999]))
        ->toThrow(DomainException::class);
});
