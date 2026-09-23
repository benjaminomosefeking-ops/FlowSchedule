<?php

namespace Tests\Unit\Domain\Scheduling\Services;

use Tests\TestCase;
use Mockery;
use App\Bundle\FlowScheduler\Domain\Services\SchedulingValidator;
use App\Bundle\FlowScheduler\Domain\Entities\Employee;
use App\Bundle\FlowScheduler\Domain\Entities\Shift;
use App\Bundle\FlowScheduler\Domain\ValueObjects\VOBEmployeeId;
use App\Bundle\FlowScheduler\Domain\ValueObjects\VOBShiftId;
use App\Bundle\FlowScheduler\Domain\ValueObjects\TimeSlot;
use App\Bundle\FlowScheduler\Domain\Enums\Skill;
use App\Bundle\FlowScheduler\Domain\Enums\ShiftType;
use App\Bundle\FlowScheduler\Domain\Ports\AssignmentRepositoryInterface;
use App\Bundle\FlowScheduler\Domain\Exceptions\UnauthorizedSkillException;

class SchedulingValidatorTest extends TestCase
{
    private $repository;
    private $validator;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->repository = Mockery::mock(AssignmentRepositoryInterface::class);
        $this->validator = new SchedulingValidator($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function createEmployeeWithSkill(Skill $skill): Employee
    {
        $id = new VOBEmployeeId('550e8400-e29b-41d4-a716-446655440000');
        return new Employee($id, 'Juan Pérez', 'juan@example.com', [$skill]);
    }

    private function createShiftWithSkill(Skill $skill): Shift
    {
        $id = new VOBShiftId('6ba7b810-9dad-11d1-80b4-00c04fd430c8');
        $start = new \DateTimeImmutable('2025-01-15 09:00:00');
        $end = new \DateTimeImmutable('2025-01-15 17:00:00');
        $timeSlot = new TimeSlot($start, $end);
        
        return new Shift(
            $id,
            'Turno de Mañana',
            'Descripción del turno',
            ShiftType::MORNING,
            $skill,
            $timeSlot
        );
    }

    /** @test */
    public function it_validates_skill_requirement()
    {
        $this->expectException(UnauthorizedSkillException::class);
        $this->expectExceptionMessage('no tiene la habilidad requerida: doctor');
        
        $employee = $this->createEmployeeWithSkill(Skill::NURSE);
        $shift = $this->createShiftWithSkill(Skill::DOCTOR);
        
        $this->repository
            ->shouldReceive('findByEmployeeAndDateRange')
            ->andReturn([]);
        
        $this->validator->validate($employee, $shift);
    }

    /** @test */
    public function it_allows_employee_with_correct_skill()
    {
        $employee = $this->createEmployeeWithSkill(Skill::NURSE);
        $shift = $this->createShiftWithSkill(Skill::NURSE);
        
        $this->repository
            ->shouldReceive('findByEmployeeAndDateRange')
            ->andReturn([]);
        
        $result = $this->validator->canAssign($employee, $shift);
        
        $this->assertTrue($result);
    }

    /** @test */
    public function it_detects_insufficient_rest()
    {
        $this->markTestIncomplete('Prueba pendiente de implementar');
    }
}
