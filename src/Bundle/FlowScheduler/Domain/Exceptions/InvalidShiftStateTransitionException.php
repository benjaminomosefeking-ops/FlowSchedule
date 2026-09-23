<?php

namespace App\Bundle\FlowScheduler\Domain\Exceptions;

class InvalidShiftStateTransitionException extends \InvalidArgumentException
{
    public function __construct(string $message = "Transición de estado de turno no válida.", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}