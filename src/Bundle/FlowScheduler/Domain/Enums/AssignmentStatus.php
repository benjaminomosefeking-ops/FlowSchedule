<?php

namespace App\Bundle\FlowScheduler\Domain\Enums;

enum AssignmentStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}