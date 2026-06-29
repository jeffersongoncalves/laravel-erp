<?php

use JeffersonGoncalves\Erp\Buying\Models\Supplier;
use JeffersonGoncalves\Erp\Buying\Models\SupplierGroup;
use JeffersonGoncalves\Erp\Core\Enums\AddressType;

it('creates a supplier with sensible defaults', function () {
    $supplier = Supplier::factory()->create(['supplier_name' => 'Acme Supplies']);

    expect($supplier->supplier_name)->toBe('Acme Supplies')
        ->and($supplier->supplier_type)->toBe('Company')
        ->and($supplier->default_currency)->toBe('USD')
        ->and($supplier->disabled)->toBeFalse();
});

it('belongs to a supplier group', function () {
    $group = SupplierGroup::factory()->create();
    $supplier = Supplier::factory()->create(['supplier_group_id' => $group->id]);

    expect($supplier->supplierGroup->is($group))->toBeTrue()
        ->and($group->suppliers)->toHaveCount(1);
});

it('nests supplier groups as a hierarchy', function () {
    $parent = SupplierGroup::factory()->group()->create();
    $child = SupplierGroup::factory()->create(['parent_supplier_group_id' => $parent->id]);

    expect($parent->is_group)->toBeTrue()
        ->and($child->parent->is($parent))->toBeTrue()
        ->and($parent->children)->toHaveCount(1);
});

it('owns polymorphic addresses and contacts', function () {
    $supplier = Supplier::factory()->create();

    $supplier->addresses()->create([
        'address_type' => AddressType::Billing,
        'address_line1' => '1 Market St',
        'city' => 'Lisbon',
        'country' => 'Portugal',
    ]);

    $supplier->contacts()->create([
        'first_name' => 'Maria',
        'email' => 'maria@example.com',
    ]);

    expect($supplier->addresses)->toHaveCount(1)
        ->and($supplier->addresses->first()->city)->toBe('Lisbon')
        ->and($supplier->contacts)->toHaveCount(1)
        ->and($supplier->contacts->first()->first_name)->toBe('Maria');
});
