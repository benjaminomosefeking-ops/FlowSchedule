<?php


namespace App\Bundle\FlowScheduler\Domain\Entities;
use App\Bundle\FlowScheduler\Domain\ValueObjects\VOBShiftId;
use App\Bundle\FlowScheduler\Domain\Enums\ShiftType;
use App\Bundle\FlowScheduler\Domain\Enums\Skill;
use App\Bundle\FlowScheduler\Domain\ValueObjects\VOBTimeSlot;
use App\Bundle\FlowScheduler\Domain\Enums\AssignmentStatus;
use App\Bundle\FlowScheduler\Domain\Exceptions\InvalidShiftStateTransitionException;


class Shift
{
    private VOBShiftId $id;
    private string $titulo;
    private string $descripcion;
    private ShiftType $tipo;
    private Skill $habilidad;
    private VOBTimeSlot $horario;
    private AssignmentStatus $status;
    private ?string $userId;


    public function __construct(VOBShiftId $id, string $titulo, string $descripcion, ShiftType $tipo, Skill $habilidad, VOBTimeSlot $horario, AssignmentStatus $status = AssignmentStatus::PENDING, ?string $userId = null)
    {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->descripcion = $descripcion;
        $this->tipo = $tipo;
        $this->habilidad = $habilidad;
        $this->horario = $horario;
        $this->status = $status;
        $this->userId = $userId;
    }

    //cambia al estado confirmado
   public function confirm(): void
    {
        if ($this->status !== AssignmentStatus::PENDING) {
            throw new InvalidShiftStateTransitionException(
                "Solo se pueden confirmar turnos en estado PENDING. Estado actual: {$this->status->value}"
            );
        }
        $this->status = AssignmentStatus::CONFIRMED;
    }

    public function complete(): void
    {
        if ($this->status !== AssignmentStatus::CONFIRMED) {
            throw new InvalidShiftStateTransitionException(
                "Solo se pueden completar turnos en estado CONFIRMED. Estado actual: {$this->status->value}"
            );
        }
        $this->status = AssignmentStatus::COMPLETED;
    }

    public function cancel(): void
    {
        if ($this->status === AssignmentStatus::COMPLETED) {
            throw new InvalidShiftStateTransitionException(
                "No se pueden cancelar turnos ya completados."
            );
        }
        $this->status = AssignmentStatus::CANCELLED;
    }



    //getters
    public function getId():VOBShiftId
    {
        return $this->id;
    }

    public function getTitulo():string
    {
        return $this->titulo;
    }

    public function getDescripcion():string
    {
        return $this->descripcion;
    }

    public function getTipo():ShiftType
    {
        return $this->tipo;
    }

    public function getHabilidad():Skill
    {
        return $this->habilidad;
    }

    public function getHorario():VOBTimeSlot
    {
        return $this->horario;
    }

    public function getEstado():AssignmentStatus
    {
        return $this->status;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

}