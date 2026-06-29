<?php

namespace JeffersonGoncalves\Erp\Core\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Core\Enums\AddressType;
use JeffersonGoncalves\Erp\Core\Models\Address;
use JeffersonGoncalves\Erp\Core\Models\Company;

/** @extends Factory<Address> */
class AddressFactory extends Factory
{
    protected $model = Address::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'address_type' => AddressType::Billing,
            'address_line1' => fake()->streetAddress(),
            'address_line2' => null,
            'city' => fake()->city(),
            'state' => fake()->state(),
            'country' => fake()->country(),
            'pincode' => fake()->postcode(),
            'addressable_type' => (new Company)->getMorphClass(),
            'addressable_id' => Company::factory(),
            'is_primary' => false,
        ];
    }

    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }
}
