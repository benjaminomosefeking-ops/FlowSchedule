<?php

namespace App\Bundle\FlowScheduler\Application\UseCases;

use App\Bundle\FlowScheduler\Domain\Ports\AuthenticatorInterface;

final readonly class AuthenticateUserUseCase
{
    public function __construct(private AuthenticatorInterface $authenticator)
    {
    }

    public function execute(string $email, string $password, bool $remember): bool
    {
        return $this->authenticator->attempt($email, $password, $remember);
    }
}
