<?php

namespace App\Bundle\FlowScheduler\Application\DTOs;

final readonly class RegisterUserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {
    }
}
