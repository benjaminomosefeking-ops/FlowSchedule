<?php

namespace Tests\Unit\Domain\Scheduling\ValueObjects;

use Tests\TestCase;
use App\Bundle\FlowScheduler\Domain\ValueObjects\TimeSlot;
use App\Bundle\FlowScheduler\Domain\Exceptions\InvalidTimeSlotException;

class TimeSlotTest extends TestCase
{
    /** @test */
    public function it_creates_a_valid_time_slot()
    {
        $start = new \DateTimeImmutable('2025-01-15 09:00:00');
        $end = new \DateTimeImmutable('2025-01-15 17:00:00');
        
        $timeSlot = new TimeSlot($start, $end);
        
        $this->assertEquals($start, $timeSlot->getStart());
        $this->assertEquals($end, $timeSlot->getEnd());
        $this->assertEquals(480, $timeSlot->getDurationInMinutes());
    }

    /** @test */
    public function it_throws_exception_when_start_is_after_end()
    {
        $this->expectException(InvalidTimeSlotException::class);
        $this->expectExceptionMessage('La hora de inicio debe ser anterior a la hora de fin.');
        
        $start = new \DateTimeImmutable('2025-01-15 17:00:00');
        $end = new \DateTimeImmutable('2025-01-15 09:00:00');
        
        new TimeSlot($start, $end);
    }

    /** @test */
    public function it_throws_exception_when_duration_is_less_than_30_minutes()
    {
        $this->expectException(InvalidTimeSlotException::class);
        $this->expectExceptionMessage('El turno debe durar al menos 30 minutos.');
        
        $start = new \DateTimeImmutable('2025-01-15 09:00:00');
        $end = new \DateTimeImmutable('2025-01-15 09:15:00');
        
        new TimeSlot($start, $end);
    }
}
