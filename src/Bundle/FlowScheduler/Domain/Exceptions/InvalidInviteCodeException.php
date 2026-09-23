<?php


namespace App\Bundle\FlowScheduler\Domain\Exceptions;


class InvalidInviteCodeException extends \InvalidArgumentException
{
    public function __construct(string $message = "Código de invitación inválido.", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
