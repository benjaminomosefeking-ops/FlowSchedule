<?php

namespace App\Bundle\FlowScheduler\Domain\Exceptions;

class InsufficientRestException extends \DomainException
{
    public function __construct(string $message = "Descanso insuficiente entre turnos")
    {
        parent::__construct($message);
    }
}