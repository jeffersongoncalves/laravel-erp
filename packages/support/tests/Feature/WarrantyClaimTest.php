<?php

use JeffersonGoncalves\Erp\Support\Enums\WarrantyClaimStatus;
use JeffersonGoncalves\Erp\Support\Models\WarrantyClaim;

it('creates a warranty claim with the default status', function () {
    $claim = WarrantyClaim::factory()->create();

    expect($claim->status)->toBeInstanceOf(WarrantyClaimStatus::class)
        ->and($claim->status)->toBe(WarrantyClaimStatus::Open)
        ->and($claim->party_type)->toBe('Customer')
        ->and($claim->company->id)->toBe($claim->company_id);
});

it('casts the warranty claim status to the enum', function () {
    $claim = WarrantyClaim::factory()->create(['status' => 'In Progress']);

    expect($claim->status)->toBe(WarrantyClaimStatus::InProgress);
});

it('resolves a warranty claim', function () {
    $claim = WarrantyClaim::factory()->create([
        'serial_no' => 'SN-0001-ABCD',
        'item_code' => 'ITEM-0001',
    ]);

    $claim->update([
        'status' => WarrantyClaimStatus::Resolved->value,
        'resolution_details' => 'Replaced under warranty.',
    ]);

    expect($claim->fresh()->status)->toBe(WarrantyClaimStatus::Resolved)
        ->and($claim->fresh()->resolution_details)->toBe('Replaced under warranty.')
        ->and($claim->serial_no)->toBe('SN-0001-ABCD');
});
