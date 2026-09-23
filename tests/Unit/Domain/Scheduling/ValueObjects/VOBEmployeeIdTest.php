<?php

namespace Tests\Unit\Domain\Scheduling\ValueObjects;

use Tests\TestCase;
use App\Bundle\FlowScheduler\Domain\ValueObjects\VOBEmployeeId;
use App\Bundle\FlowScheduler\Domain\Exceptions\InvalidEmployeeIdException;

class VOBEmployeeIdTest extends TestCase
{
    /** @test */
    public function it_creates_a_valid_employee_id()
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $id = new VOBEmployeeId($uuid);
        
        $this->assertEquals($uuid, $id->value());
        $this->assertEquals($uuid, (string) $id);
    }

    /** @test */
    public function it_throws_exception_for_invalid_uuid()
    {
        $this->expectException(InvalidEmployeeIdException::class);
        $this->expectExceptionMessage('El ID del empleado no es válido. Debe ser un UUID.');
        
        new VOBEmployeeId('invalid-uuid');
    }

    /** @test */
    public function it_checks_equality()
    {
        $uuid1 = '550e8400-e29b-41d4-a716-446655440000';
        $uuid2 = '550e8400-e29b-41d4-a716-446655440000';
        $uuid3 = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';
        
        $id1 = new VOBEmployeeId($uuid1);
        $id2 = new VOBEmployeeId($uuid2);
        $id3 = new VOBEmployeeId($uuid3);
        
        $this->assertTrue($id1->equals($id2));
        $this->assertFalse($id1->equals($id3));
    }
}
