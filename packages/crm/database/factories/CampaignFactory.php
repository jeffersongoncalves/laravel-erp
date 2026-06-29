<?php

namespace JeffersonGoncalves\Erp\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JeffersonGoncalves\Erp\Crm\Models\Campaign;

/** @extends Factory<Campaign> */
class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'campaign_name' => fake()->unique()->words(3, true),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
