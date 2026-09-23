<?php

namespace App\Bundle\FlowScheduler\Infrastructure\Persistence\Repositories;

use App\Bundle\FlowScheduler\Domain\Ports\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

final class EloquentUserRepository implements UserRepositoryInterface
{
    public function create(string $name, string $email, string $password): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'employee', // Por defecto empleado
            'onboarding_completed' => false, // Debe completar el onboarding
        ]);
    }
}
