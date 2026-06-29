<?php

use JeffersonGoncalves\Erp\Selling\Models\Customer;
use JeffersonGoncalves\Erp\Selling\Models\CustomerGroup;

it('creates a customer with default attributes', function () {
    $customer = Customer::factory()->create();

    expect($customer->customer_type)->toBe('Company')
        ->and($customer->default_currency)->toBe('USD')
        ->and($customer->credit_limit)->toBe(0.0)
        ->and($customer->disabled)->toBeFalse();
});

it('casts customer attributes', function () {
    $customer = Customer::factory()->create([
        'credit_limit' => '1500.50',
        'disabled' => 1,
    ]);

    expect($customer->credit_limit)->toBeFloat()->toBe(1500.5)
        ->and($customer->disabled)->toBeBool()->toBeTrue();
});

it('relates a customer to its group', function () {
    $group = CustomerGroup::factory()->create();
    $customer = Customer::factory()->create(['customer_group_id' => $group->id]);

    expect($customer->customerGroup->id)->toBe($group->id)
        ->and($group->customers->pluck('id'))->toContain($customer->id);
});

it('builds a tree of customer groups', function () {
    $parent = CustomerGroup::factory()->group()->create();
    $child = CustomerGroup::factory()->create(['parent_customer_group_id' => $parent->id]);

    expect($parent->is_group)->toBeTrue()
        ->and($child->parent->id)->toBe($parent->id)
        ->and($parent->children->pluck('id'))->toContain($child->id);
});

it('attaches addresses and contacts through morphs', function () {
    $customer = Customer::factory()->create();

    $customer->addresses()->create([
        'address_line1' => '123 Market Street',
        'city' => 'Lisbon',
        'country' => 'Portugal',
    ]);

    $customer->contacts()->create([
        'first_name' => 'Ana',
        'last_name' => 'Silva',
        'email' => 'ana@example.com',
    ]);

    $customer->refresh();

    expect($customer->addresses)->toHaveCount(1)
        ->and($customer->addresses->first()->addressable_type)->toBe($customer->getMorphClass())
        ->and($customer->addresses->first()->addressable->is($customer))->toBeTrue()
        ->and($customer->contacts)->toHaveCount(1)
        ->and($customer->contacts->first()->contactable->is($customer))->toBeTrue();
});
