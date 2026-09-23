<?php

namespace Tests\Unit\Domain\Scheduling\Entities;

use Tests\TestCase;
use App\Bundle\FlowScheduler\Domain\Entities\Employee;
use App\Bundle\FlowScheduler\Domain\ValueObjects\VOBEmployeeId;
use App\Bundle\FlowScheduler\Domain\Enums\Skill;
use App\Bundle\FlowScheduler\Domain\Exceptions\SkillAlreadyExistsException;

class EmployeeTest extends TestCase
{
    private function createEmployee(): Employee
    {
        $id = new VOBEmployeeId('550e8400-e29b-41d4-a716-446655440000');
        return new Employee(
            $id,
            'Juan Pérez',
            'juan@example.com',
            [Skill::NURSE]
        );
    }

    /** @test */
    public function it_creates_an_employee()
    {
        $employee = $this->createEmployee();
        
        $this->assertEquals('Juan Pérez', $employee->getNombre());
        $this->assertEquals('juan@example.com', $employee->getEmail());
        $this->assertTrue($employee->hasSkill(Skill::NURSE));
        $this->assertEquals(0, $employee->getWeekendCount());
    }

    /** @test */
    public function it_adds_a_skill()
    {
        $employee = $this->createEmployee();
        
        $this->assertFalse($employee->hasSkill(Skill::DOCTOR));
        
        $employee->addSkill(Skill::DOCTOR);
        
        $this->assertTrue($employee->hasSkill(Skill::DOCTOR));
        $this->assertTrue($employee->hasSkill(Skill::NURSE));
    }

    /** @test */
    public function it_throws_exception_when_adding_duplicate_skill()
    {
        $this->expectException(SkillAlreadyExistsException::class);
        
        $employee = $this->createEmployee();
        $employee->addSkill(Skill::NURSE);
    }

    /** @test */
    public function it_increments_weekend_count()
    {
        $employee = $this->createEmployee();
        
        $this->assertEquals(0, $employee->getWeekendCount());
        
        $employee->incrementWeekendCount();
        $this->assertEquals(1, $employee->getWeekendCount());
        
        $employee->incrementWeekendCount();
        $this->assertEquals(2, $employee->getWeekendCount());
    }
}
