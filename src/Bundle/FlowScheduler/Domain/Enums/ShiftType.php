<?php

namespace App\Bundle\FlowScheduler\Domain\Enums;

enum ShiftType: string
{
    case MORNING = 'morning';
    case AFTERNOON = 'afternoon';
    case NIGHT = 'night';
}