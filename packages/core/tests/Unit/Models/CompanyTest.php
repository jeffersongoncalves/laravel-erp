<?php

use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Core\Models\Department;

it('uses the configured table prefix', function () {
    expect((new Company)->getTable())->toBe('erp_companies');
});

it('respects a custom table prefix', function () {
    config(['erp-core.table_prefix' => 'custom_']);

    expect((new Company)->getTable())->toBe('custom_companies');

    config(['erp-core.table_prefix' => 'erp_']);
});

it('can be created via factory', function () {
    $company = Company::factory()->create(['name' => 'Acme Inc']);

    expect($company->name)->toBe('Acme Inc')
        ->and($company->is_group)->toBeFalse();
});

it('has a parent and children relationship', function () {
    $parent = Company::factory()->group()->create();
    $child = Company::factory()->create(['parent_company_id' => $parent->id]);

    expect($child->parent->id)->toBe($parent->id)
        ->and($parent->children)->toHaveCount(1)
        ->and($parent->is_group)->toBeTrue();
});

it('has departments', function () {
    $company = Company::factory()->create();
    Department::factory()->forCompany($company)->create();

    expect($company->departments)->toHaveCount(1);
});
