<?php

namespace Tests\Feature;

use App\Enums\ProposalStatus;
use App\Models\Proposal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AvailableProposalStatusTransitionsTest extends TestCase
{

    #[Test]
    public function testDraftToStatus(): void
    {

        $proposal = new Proposal();

        $proposal->status = ProposalStatus::DRAFT;

        $result = [
            $proposal->status->ableToTransitionStatus(ProposalStatus::DRAFT),
            $proposal->status->ableToTransitionStatus(ProposalStatus::SUBMITTED),
            $proposal->status->ableToTransitionStatus(ProposalStatus::REJECTED),
            $proposal->status->ableToTransitionStatus(ProposalStatus::APPROVED),
            $proposal->status->ableToTransitionStatus(ProposalStatus::CANCELED),
        ];

        $this->assertEquals([false,true,false,false,true], $result);

    }

    #[Test]
    public function testSubmittedToStatus(): void
    {

        $proposal = new Proposal();

        $proposal->status = ProposalStatus::SUBMITTED;

        $result = [
            $proposal->status->ableToTransitionStatus(ProposalStatus::DRAFT),
            $proposal->status->ableToTransitionStatus(ProposalStatus::SUBMITTED),
            $proposal->status->ableToTransitionStatus(ProposalStatus::REJECTED),
            $proposal->status->ableToTransitionStatus(ProposalStatus::APPROVED),
            $proposal->status->ableToTransitionStatus(ProposalStatus::CANCELED),
        ];

        $this->assertEquals([false,false,true,true,true], $result);

    }

    #[Test]
    public function testRejectedToStatus(): void
    {

        $proposal = new Proposal();

        $proposal->status = ProposalStatus::REJECTED;

        $result = [
            $proposal->status->ableToTransitionStatus(ProposalStatus::DRAFT),
            $proposal->status->ableToTransitionStatus(ProposalStatus::SUBMITTED),
            $proposal->status->ableToTransitionStatus(ProposalStatus::REJECTED),
            $proposal->status->ableToTransitionStatus(ProposalStatus::APPROVED),
            $proposal->status->ableToTransitionStatus(ProposalStatus::CANCELED),
        ];

        $this->assertEquals([false,true,false,false,false], $result);

    }

    #[Test]
    public function testApprovedToStatus(): void
    {

        $proposal = new Proposal();

        $proposal->status = ProposalStatus::APPROVED;

        $result = [
            $proposal->status->ableToTransitionStatus(ProposalStatus::DRAFT),
            $proposal->status->ableToTransitionStatus(ProposalStatus::SUBMITTED),
            $proposal->status->ableToTransitionStatus(ProposalStatus::REJECTED),
            $proposal->status->ableToTransitionStatus(ProposalStatus::APPROVED),
            $proposal->status->ableToTransitionStatus(ProposalStatus::CANCELED),
        ];

        $this->assertEquals([false,false,false,false,false], $result);

    }

    #[Test]
    public function testCanceledToStatus(): void
    {

        $proposal = new Proposal();

        $proposal->status = ProposalStatus::CANCELED;

        $result = [
            $proposal->status->ableToTransitionStatus(ProposalStatus::DRAFT),
            $proposal->status->ableToTransitionStatus(ProposalStatus::SUBMITTED),
            $proposal->status->ableToTransitionStatus(ProposalStatus::REJECTED),
            $proposal->status->ableToTransitionStatus(ProposalStatus::APPROVED),
            $proposal->status->ableToTransitionStatus(ProposalStatus::CANCELED),
        ];

        $this->assertEquals([false,false,false,false,false], $result);

    }
}
