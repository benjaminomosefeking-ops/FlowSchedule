<?php
namespace Tests\Unit\Domain\Scheduling\ValueObjects;

use Tests\TestCase;
use App\Bundle\FlowScheduler\Domain\ValueObjects\VOBShiftId;
use App\Bundle\FlowScheduler\Domain\Exceptions\InvalidShiftIdException;

class VOBShiftIdTest extends TestCase
{
    /** @test */
    public function it_creates_a_valid_shift_id()
    {
        $uuid = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';
        $id = new VOBShiftId($uuid);
        
        $this->assertEquals($uuid, $id->value());
        $this->assertEquals($uuid, (string) $id);
    }

    /** @test */
    public function it_throws_exception_for_invalid_uuid()
    {
        $this->expectException(InvalidShiftIdException::class);
        
        new VOBShiftId('invalid-uuid');
    }

    /** @test */
    public function it_checks_equality()
    {
        $uuid1 = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';
        $uuid2 = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';
        $uuid3 = '6ba7b810-9dad-11d1-80b4-00c04fd430c9';
        
        $id1 = new VOBShiftId($uuid1);
        $id2 = new VOBShiftId($uuid2);
        $id3 = new VOBShiftId($uuid3);
        
        $this->assertTrue($id1->equals($id2));
        $this->assertFalse($id1->equals($id3));
    }
}
