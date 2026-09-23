<?php

namespace App\Bundle\FlowScheduler\UI\Controllers;

use App\Bundle\FlowScheduler\Application\UseCases\CreateShiftUseCase;
use App\Bundle\FlowScheduler\Application\DTOs\CreateShiftDTO;
use App\Bundle\FlowScheduler\Domain\ValueObjects\VOBTimeSlot;
use App\Bundle\FlowScheduler\Domain\Enums\ShiftType;
use App\Bundle\FlowScheduler\Domain\Enums\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ShiftController
{
    public function store(Request $request, CreateShiftUseCase $useCase): JsonResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:morning,afternoon,night'],
            'required_skill' => ['required', 'in:nurse,doctor,xray'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
        ]);

        try {
            $start = new \DateTimeImmutable($request->start_time);
            $end = new \DateTimeImmutable($request->end_time);
            $timeSlot = new VOBTimeSlot($start, $end);

            $dto = new CreateShiftDTO(
                title: $request->title,
                description: $request->description,
                type: ShiftType::from($request->type),
                requiredSkill: Skill::from($request->required_skill),
                timeSlot: $timeSlot,
                userId: Auth::id()
            );

            $shift = $useCase->execute($dto);

            return response()->json([
                'message' => 'Turno creado exitosamente',
                'data' => [
                    'id' => $shift->getId()->getNombre(),
                    'title' => $shift->getTitulo(),
                    'description' => $shift->getDescripcion(),
                    'type' => $shift->getTipo()->value,
                    'required_skill' => $shift->getHabilidad()->value,
                    'start_time' => $shift->getHorario()->getStart()->format('Y-m-d H:i:s'),
                    'end_time' => $shift->getHorario()->getEnd()->format('Y-m-d H:i:s'),
                    'status' => $shift->getEstado()->value
                ]
            ], 201);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
    }
}
