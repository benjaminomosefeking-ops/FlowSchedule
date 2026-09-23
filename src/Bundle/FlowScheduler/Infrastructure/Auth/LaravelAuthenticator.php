<?php

namespace App\Bundle\FlowScheduler\Infrastructure\Auth;

use App\Bundle\FlowScheduler\Domain\Ports\AuthenticatorInterface;
use Illuminate\Support\Facades\Auth;

final class LaravelAuthenticator implements AuthenticatorInterface
{
    public function attempt(string $email, string $password, bool $remember): bool
    {
        return Auth::attempt(['email' => $email, 'password' => $password], $remember);
    }
}
