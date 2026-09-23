<?php

namespace App\Bundle\FlowScheduler\Application\UseCases;

use App\Bundle\FlowScheduler\Application\DTOs\CreateShiftDTO;
use App\Bundle\FlowScheduler\Domain\Entities\Shift;
use App\Bundle\FlowScheduler\Domain\ValueObjects\VOBShiftId;
use App\Bundle\FlowScheduler\Domain\Ports\ShiftRepositoryInterface;

class CreateShiftUseCase
{
    public function __construct(
        private ShiftRepositoryInterface $shiftRepository
    ) {}

    public function execute(CreateShiftDTO $dto): Shift
    {
        // Generar un nuevo ID para el turno
        $shiftId = new VOBShiftId((string) \Illuminate\Support\Str::uuid());

        // Crear el turno
        $shift = new Shift(
            $shiftId,
            $dto->title,
            $dto->description,
            $dto->type,
            $dto->requiredSkill,
            $dto->timeSlot,
            userId: $dto->userId
        );

        // Guardar
        $this->shiftRepository->save($shift);

        return $shift;
    }
}