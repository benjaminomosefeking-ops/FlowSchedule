<?php

namespace App\Bundle\FlowScheduler\Domain\Exceptions;

class UnfairDistributionException extends \DomainException
{
    public function __construct(string $message = "Distribución injusta de turnos")
    {
        parent::__construct($message);
    }
}