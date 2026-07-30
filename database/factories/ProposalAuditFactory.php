<?php

namespace Database\Factories;

use App\Enums\ProposalAuditEvent;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProposalAuditFactory extends Factory
{
    public function definition(): array
    {

        return [
            'proposal_id' => Proposal::factory(),
            'actor' => fake()->name(),
            'event' => fake()->randomElement(ProposalAuditEvent::cases()),
            'payload' => json_encode([
                'id' => 2,
                'origin' => 'APP',
                'status' => 'DRAFT',
                'product' => 'Netflix',
                'client_id' => 1,
                'created_at' => '2026-07-30T06:37:03.000000Z',
                'updated_at' => '2026-07-30T06:37:03.000000Z',
                'monthly_value' => '49.99',
            ]),
        ];
    }
}
