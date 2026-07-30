<?php

namespace Tests\Feature;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Str;
use \Symfony\Component\HttpKernel\Exception\HttpException;

class OptimisticLockingSavingTest extends TestCase
{

    use RefreshDatabase;

    public function testVersionConflict(): void
    {
        //experando exception...
        $this->expectException(HttpException::class);

        $proposal = Proposal::factory()->create(['version' => 1, 'status' => 'DRAFT']);

        $proposalInstanceA = Proposal::find($proposal->id);
        $proposalInstanceB = Proposal::find($proposal->id);

        $proposalInstanceA->status = 'SUBMITTED';
        $proposalInstanceA->save();

        $proposalInstanceB->status = 'APPROVED';
        $proposalInstanceB->save();

    }

}
