<?php

use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Hr\Models\Employee;
use JeffersonGoncalves\Erp\Hr\Models\PayrollEntry;
use JeffersonGoncalves\Erp\Hr\Models\SalaryComponent;
use JeffersonGoncalves\Erp\Hr\Models\SalaryStructure;
use JeffersonGoncalves\Erp\Hr\Models\SalaryStructureAssignment;
use JeffersonGoncalves\Erp\Hr\Services\PayrollEntryService;

it('creates one draft salary slip per active assigned employee', function () {
    $company = Company::factory()->create();

    $basic = SalaryComponent::factory()->earning()->create();
    $tax = SalaryComponent::factory()->deduction()->create();

    $structure = SalaryStructure::factory()->create(['company_id' => $company->id]);
    $structure->components()->create(['salary_component_id' => $basic->id, 'amount' => 4000]);
    $structure->components()->create(['salary_component_id' => $tax->id, 'amount' => 400]);

    // Two active employees with an assignment.
    foreach (range(1, 2) as $i) {
        $employee = Employee::factory()->create([
            'company_id' => $company->id,
            'date_of_joining' => '2020-01-01',
        ]);

        SalaryStructureAssignment::factory()->create([
            'employee_id' => $employee->id,
            'salary_structure_id' => $structure->id,
            'from_date' => '2020-01-01',
        ]);
    }

    // An employee who has left must be skipped.
    Employee::factory()->left()->create([
        'company_id' => $company->id,
        'date_of_joining' => '2020-01-01',
        'date_of_leaving' => '2021-01-01',
    ]);

    // An active employee without an assignment must be skipped.
    Employee::factory()->create([
        'company_id' => $company->id,
        'date_of_joining' => '2020-01-01',
    ]);

    $entry = PayrollEntry::factory()->create([
        'company_id' => $company->id,
        'start_date' => '2024-12-01',
        'end_date' => '2024-12-31',
    ]);

    $slips = app(PayrollEntryService::class)->createSalarySlips($entry);

    expect($slips)->toHaveCount(2);

    $slips->each(function ($slip) {
        expect($slip->gross_pay)->toBe(4000.0)
            ->and($slip->total_deduction)->toBe(400.0)
            ->and($slip->net_pay)->toBe(3600.0)
            ->and($slip->isDraft())->toBeTrue();
    });
});
