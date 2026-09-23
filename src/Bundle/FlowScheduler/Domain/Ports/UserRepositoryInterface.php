<?php

namespace App\Bundle\FlowScheduler\Domain\Ports;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(string $name, string $email, string $password): User;
}
