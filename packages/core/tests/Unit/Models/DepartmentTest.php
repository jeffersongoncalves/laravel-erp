<?php

use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Core\Models\Department;

it('uses the configured table prefix', function () {
    expect((new Department)->getTable())->toBe('erp_departments');
});

it('belongs to a company via the HasCompany concern', function () {
    $company = Company::factory()->create();
    $department = Department::factory()->forCompany($company)->create();

    expect($department->company->id)->toBe($company->id);
});

it('scopes departments for a company', function () {
    $company = Company::factory()->create();
    Department::factory()->forCompany($company)->create();
    Department::factory()->create();

    expect(Department::forCompany($company->id)->count())->toBe(1);
});

it('has a parent and children relationship', function () {
    $parent = Department::factory()->create();
    $child = Department::factory()->create(['parent_department_id' => $parent->id]);

    expect($child->parent->id)->toBe($parent->id)
        ->and($parent->children)->toHaveCount(1);
});
