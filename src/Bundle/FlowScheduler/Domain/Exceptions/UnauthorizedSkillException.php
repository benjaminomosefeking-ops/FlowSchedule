<?php

namespace App\Bundle\FlowScheduler\Domain\Exceptions;

class UnauthorizedSkillException extends \DomainException
{
    public function __construct(string $message = "Habilidad no autorizada para este turno")
    {
        parent::__construct($message);
    }
}