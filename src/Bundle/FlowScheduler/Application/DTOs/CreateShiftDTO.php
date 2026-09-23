<?php

namespace App\Bundle\FlowScheduler\Application\DTOs;

use App\Bundle\FlowScheduler\Domain\Enums\ShiftType;
use App\Bundle\FlowScheduler\Domain\Enums\Skill;
use App\Bundle\FlowScheduler\Domain\ValueObjects\VOBTimeSlot;

class CreateShiftDTO
{
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly ShiftType $type,
        public readonly Skill $requiredSkill,
        public readonly VOBTimeSlot $timeSlot,
        public readonly string $userId
    ) {}
}