<?php

namespace App\Bundle\FlowScheduler\Application\UseCases;

use App\Bundle\FlowScheduler\Application\DTOs\RegisterUserDTO;
use App\Bundle\FlowScheduler\Domain\Ports\UserRepositoryInterface;
use App\Models\User;

final readonly class RegisterUserUseCase
{
    public function __construct(private UserRepositoryInterface $users)
    {
    }

    public function execute(RegisterUserDTO $data): User
    {
        return $this->users->create($data->name, $data->email, $data->password);
    }
}
