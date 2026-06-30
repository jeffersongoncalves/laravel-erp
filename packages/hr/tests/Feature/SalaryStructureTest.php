<?php

use JeffersonGoncalves\Erp\Core\Enums\DocStatus;
use JeffersonGoncalves\Erp\Hr\Enums\SalaryComponentType;
use JeffersonGoncalves\Erp\Hr\Models\Employee;
use JeffersonGoncalves\Erp\Hr\Models\SalaryComponent;
use JeffersonGoncalves\Erp\Hr\Models\SalaryStructure;
use JeffersonGoncalves\Erp\Hr\Models\SalaryStructureAssignment;

it('builds a salary structure with components and submits it', function () {
    $structure = SalaryStructure::factory()->create();

    $basic = SalaryComponent::factory()->earning()->create();
    $tax = SalaryComponent::factory()->deduction()->create();

    $structure->components()->create(['salary_component_id' => $basic->id, 'amount' => 5000]);
    $structure->components()->create(['salary_component_id' => $tax->id, 'amount' => 500]);

    expect($structure->components)->toHaveCount(2)
        ->and($basic->type)->toBe(SalaryComponentType::Earning)
        ->and($tax->type)->toBe(SalaryComponentType::Deduction);

    $structure->submit();

    expect($structure->docstatus)->toBe(DocStatus::Submitted);
});

it('assigns a salary structure to an employee', function () {
    $employee = Employee::factory()->create();
    $structure = SalaryStructure::factory()->create();

    $assignment = SalaryStructureAssignment::factory()->create([
        'employee_id' => $employee->id,
        'salary_structure_id' => $structure->id,
        'base' => 5000,
    ]);

    $assignment->submit();

    expect($assignment->docstatus)->toBe(DocStatus::Submitted)
        ->and($employee->salaryStructureAssignments)->toHaveCount(1)
        ->and($assignment->salaryStructure->id)->toBe($structure->id);
});
