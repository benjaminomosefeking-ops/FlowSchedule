<?php
namespace Tests\Unit\Domain\Scheduling\Entities;
use Tests\TestCase;
use App\Bundle\FlowScheduler\Domain\Entities\Assignment;
use App\Bundle\FlowScheduler\Domain\ValueObjects\VOBEmployeeId;
use App\Bundle\FlowScheduler\Domain\ValueObjects\VOBShiftId;
use App\Bundle\FlowScheduler\Domain\Enums\AssignmentStatus;
use App\Bundle\FlowScheduler\Domain\Exceptions\InvalidAssignmentStateTransitionException;
class AssignmentTest extends TestCase
{
    private function createAssignment(): Assignment
    {
        return new Assignment(
            new VOBEmployeeId('550e8400-e29b-41d4-a716-446655440000'),
            new VOBShiftId('6ba7b810-9dad-11d1-80b4-00c04fd430c8'),
            new \DateTimeImmutable('2025-01-20 08:00:00')
        );
    }
    public function test_creates_assignment(): void
    {
        $assignment = $this->createAssignment();
        $this->assertEquals(AssignmentStatus::PENDING, $assignment->getStatus());
        $this->assertTrue($assignment->isActive());
    }
    public function test_confirms_assignment(): void
    {
        $assignment = $this->createAssignment();
        $assignment->confirm();
        $this->assertEquals(AssignmentStatus::CONFIRMED, $assignment->getStatus());
    }
    public function test_completes_assignment(): void
    {
        $assignment = $this->createAssignment();
        $assignment->confirm();
        $assignment->complete();
        $this->assertEquals(AssignmentStatus::COMPLETED, $assignment->getStatus());
        $this->assertFalse($assignment->isActive());
    }
    public function test_cancels_assignment(): void
    {
        $assignment = $this->createAssignment();
        $assignment->cancel();
        $this->assertEquals(AssignmentStatus::CANCELLED, $assignment->getStatus());
        $this->assertFalse($assignment->isActive());
    }
    public function test_throws_exception_when_completing_pending_assignment(): void
    {
        $this->expectException(InvalidAssignmentStateTransitionException::class);
        $assignment = $this->createAssignment();
        $assignment->complete();
    }
    public function test_throws_exception_when_cancelling_completed_assignment(): void
    {
        $this->expectException(InvalidAssignmentStateTransitionException::class);
        $assignment = $this->createAssignment();
        $assignment->confirm();
        $assignment->complete();
        $assignment->cancel();
    }
}
