<?php

namespace App\Bundle\FlowScheduler\Domain\Ports;

interface AuthenticatorInterface
{
    public function attempt(string $email, string $password, bool $remember): bool;
}
