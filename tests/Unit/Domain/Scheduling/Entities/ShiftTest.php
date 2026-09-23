<?php

namespace Tests\Unit\Domain\Scheduling\Entities;

use Tests\TestCase;
use App\Bundle\FlowScheduler\Domain\Entities\Shift;
use App\Bundle\FlowScheduler\Domain\ValueObjects\VOBShiftId;
use App\Bundle\FlowScheduler\Domain\ValueObjects\TimeSlot;
use App\Bundle\FlowScheduler\Domain\Enums\ShiftType;
use App\Bundle\FlowScheduler\Domain\Enums\Skill;
use App\Bundle\FlowScheduler\Domain\Enums\AssignmentStatus;
use App\Bundle\FlowScheduler\Domain\Exceptions\InvalidShiftStateTransitionException;

class ShiftTest extends TestCase
{
    private function createShift(): Shift
    {
        $id = new VOBShiftId('6ba7b810-9dad-11d1-80b4-00c04fd430c8');
        $start = new \DateTimeImmutable('2025-01-15 09:00:00');
        $end = new \DateTimeImmutable('2025-01-15 17:00:00');
        $timeSlot = new TimeSlot($start, $end);
        
        return new Shift(
            $id,
            'Turno de Mañana',
            'Turno de enfermería',
            ShiftType::MORNING,
            Skill::NURSE,
            $timeSlot
        );
    }

    /** @test */
    public function it_creates_a_shift()
    {
        $shift = $this->createShift();
        
        $this->assertEquals('Turno de Mañana', $shift->getTitle());
        $this->assertEquals('Turno de enfermería', $shift->getDescription());
        $this->assertEquals(ShiftType::MORNING, $shift->getType());
        $this->assertEquals(Skill::NURSE, $shift->getRequiredSkill());
        $this->assertEquals(AssignmentStatus::PENDING, $shift->getStatus());
    }

    /** @test */
    public function it_confirms_a_shift()
    {
        $shift = $this->createShift();
        
        $this->assertEquals(AssignmentStatus::PENDING, $shift->getStatus());
        
        $shift->confirm();
        $this->assertEquals(AssignmentStatus::CONFIRMED, $shift->getStatus());
    }

    /** @test */
    public function it_completes_a_shift()
    {
        $shift = $this->createShift();
        $shift->confirm();
        
        $this->assertEquals(AssignmentStatus::CONFIRMED, $shift->getStatus());
        
        $shift->complete();
        $this->assertEquals(AssignmentStatus::COMPLETED, $shift->getStatus());
    }

    /** @test */
    public function it_cancels_a_shift()
    {
        $shift = $this->createShift();
        
        $this->assertEquals(AssignmentStatus::PENDING, $shift->getStatus());
        
        $shift->cancel();
        $this->assertEquals(AssignmentStatus::CANCELLED, $shift->getStatus());
    }

    /** @test */
    public function it_throws_exception_when_completing_a_pending_shift()
    {
        $this->expectException(InvalidShiftStateTransitionException::class);
        $this->expectExceptionMessage('Solo se pueden completar turnos en estado CONFIRMED');
        
        $shift = $this->createShift();
        $shift->complete();
    }

    /** @test */
    public function it_throws_exception_when_cancelling_a_completed_shift()
    {
        $this->expectException(InvalidShiftStateTransitionException::class);
        $this->expectExceptionMessage('No se pueden cancelar turnos ya completados');
        
        $shift = $this->createShift();
        $shift->confirm();
        $shift->complete();
        $shift->cancel();
    }
}
