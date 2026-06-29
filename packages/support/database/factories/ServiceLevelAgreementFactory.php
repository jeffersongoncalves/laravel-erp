<?php

namespace JeffersonGoncalves\Erp\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Models\Company;
use JeffersonGoncalves\Erp\Support\Models\ServiceLevelAgreement;

/** @extends Factory<ServiceLevelAgreement> */
class ServiceLevelAgreementFactory extends Factory
{
    protected $model = ServiceLevelAgreement::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'default_sla' => false,
            'enabled' => true,
            'default_priority' => 'Medium',
            'company_id' => Company::factory(),
        ];
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'default_sla' => true,
        ]);
    }
}
