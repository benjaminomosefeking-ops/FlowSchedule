<?php



namespace App\Bundle\FlowScheduler\Domain\Exceptions;


class SkillAlreadyExistsException extends \InvalidArgumentException
{
    public function __construct(string $message = "La habilidad ya existe para este empleado.", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}