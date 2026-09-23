<?php

namespace App\Bundle\FlowScheduler\Domain\ValueObjects;
use App\Bundle\FlowScheduler\Domain\Exceptions\InvalidTimeSlotException;

class VOBTimeSlot
{
    private \DateTimeImmutable $start;
    private \DateTimeImmutable $end;

    //metodo contructor

    public function __construct(\DateTimeImmutable $start, \DateTimeImmutable $end)
    {

    // validacion 1
        if ($start >= $end) {
            throw new InvalidTimeSlotException('La hora de inicio debe ser anterior a la hora de fin.');
        }


        // validacion 2
        $diff = $start->diff($end);
        $minutes = ($diff->h * 60) + $diff->i;

        if ($minutes < 30) {
            throw new InvalidTimeSlotException('El turno debe durar al menos 30 minutos.');
        }

        //resultado

        $this->start = $start;
        $this->end = $end;
    }


    //getters y setters

    public function getStart(): \DateTimeImmutable
    {
        return $this->start;
    }

    public function getEnd(): \DateTimeImmutable
    {
        return $this->end;
    }

    public function getDurationInMinutes(): int
    {
        $diff = $this->start->diff($this->end);
        return ($diff->h * 60) + $diff->i;
    }
}