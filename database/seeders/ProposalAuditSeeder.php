<?php

namespace Database\Seeders;

use App\Models\ProposalAudit;
use Illuminate\Database\Seeder;

class ProposalAuditSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProposalAudit::factory()->count(20)->create();
    }
}
