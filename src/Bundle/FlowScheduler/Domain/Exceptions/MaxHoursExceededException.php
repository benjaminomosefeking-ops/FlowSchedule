<?php

namespace App\Bundle\FlowScheduler\Domain\Exceptions;

class MaxHoursExceededException extends \DomainException
{
    public function __construct(string $message = "Límite de horas semanales excedido")
    {
        parent::__construct($message);
    }
}