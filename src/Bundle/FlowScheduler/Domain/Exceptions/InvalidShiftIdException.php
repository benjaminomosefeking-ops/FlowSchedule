<?php
namespace App\Bundle\FlowScheduler\Domain\Exceptions;

 class InvalidShiftIdException extends \InvalidArgumentException
{
    public function __construct(string $message = "El ID del turno no es válido.", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}