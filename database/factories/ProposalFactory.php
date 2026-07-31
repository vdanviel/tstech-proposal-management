<?php

namespace Database\Factories;

use App\Enums\ProposalOrigin;
use App\Models\Client;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Factories\Factory;
use \App\Enums\ProposalStatus;

/**
 * @extends Factory<Proposal>
 */
class ProposalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::query()->inRandomOrder()->value('id') ?? Client::factory(),
            'product' => $this->faker->word(),
            'monthly_value' => $this->faker->randomFloat(2, 0, 10000),
            'status' => $this->faker->randomElement(ProposalStatus::cases())->value,
            'origin' => $this->faker->randomElement(ProposalOrigin::cases())->value,
        ];
    }
}
