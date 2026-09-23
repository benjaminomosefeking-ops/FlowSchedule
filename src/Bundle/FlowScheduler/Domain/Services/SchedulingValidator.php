<?php

namespace App\Bundle\FlowScheduler\Domain\Services;

use App\Bundle\FlowScheduler\Domain\Entities\Employee;
use App\Bundle\FlowScheduler\Domain\Entities\Shift;
use App\Bundle\FlowScheduler\Domain\Ports\AssignmentRepositoryInterface;
use App\Bundle\FlowScheduler\Domain\Exceptions\InsufficientRestException;
use App\Bundle\FlowScheduler\Domain\Exceptions\MaxHoursExceededException;
use App\Bundle\FlowScheduler\Domain\Exceptions\UnauthorizedSkillException;
use App\Bundle\FlowScheduler\Domain\Exceptions\UnfairDistributionException;

class SchedulingValidator
{
    private AssignmentRepositoryInterface $assignmentRepository;

    public function __construct(AssignmentRepositoryInterface $assignmentRepository)
    {
        $this->assignmentRepository = $assignmentRepository;
    }

    /**
     * Valida todas las reglas de negocio para asignar un empleado a un turno
     * 
     * @throws InsufficientRestException
     * @throws MaxHoursExceededException
     * @throws UnauthorizedSkillException
     * @throws UnfairDistributionException
     */
    public function validate(Employee $employee, Shift $shift): void
    {
        $this->validateRest($employee, $shift);
        $this->validateMaxHours($employee, $shift);
        $this->validateSkill($employee, $shift);
        $this->validateFairness($employee, $shift);
    }

    
     // Verifica si un empleado puede ser asignado sin lanzar excepciones

    public function canAssign(Employee $employee, Shift $shift): bool
    {
        try {
            $this->validate($employee, $shift);
            return true;
        } catch (\DomainException $e) {
            return false;
        }
    }

    
     // Descanso mínimo de 12 horas entre turnos

    private function validateRest(Employee $employee, Shift $shift): void
    {
        $start = $shift->getHorario()->getStart();
        $twoDaysAgo = $start->modify('-2 days');

      //  últimos 2 días
        $previousAssignments = $this->assignmentRepository->findByEmployeeAndDateRange(
            $employee->getId(),
            $twoDaysAgo,
            $start
        );

        foreach ($previousAssignments as $assignment) {
            if (method_exists($assignment, 'getShift')) {
                $previousShift = $assignment->getShift();
                $previousEnd = $previousShift->getTimeSlot()->getEnd();
                
                // Calcular horas de descanso
                $diff = $previousEnd->diff($start);
                $hours = ($diff->days * 24) + $diff->h;
                
                if ($hours < 12) {
                    throw new InsufficientRestException(
                        "El empleado {$employee->getNombre()} debe descansar al menos 12 horas. "
                        . "Solo han pasado {$hours} horas desde su último turno."
                    );
                }
            }
        }
    }

    
        //Límite máximo de 40 horas semanales
     
    private function validateMaxHours(Employee $employee, Shift $shift): void
    {
        $start = $shift->getHorario()->getStart();
        
        // Calcular el inicio de la semana (lunes)
        $weekStart = clone $start;
        $weekStart->modify('monday this week');
        $weekStart->setTime(0, 0, 0);
        
        // Calcular el fin de la semana (domingo)
        $weekEnd = clone $start;
        $weekEnd->modify('sunday this week');
        $weekEnd->setTime(23, 59, 59);

        // Buscar asignaciones del empleado en esta semana
        $weeklyAssignments = $this->assignmentRepository->findByEmployeeAndDateRange(
            $employee->getId(),
            $weekStart,
            $weekEnd
        );

        // horas totales ya trabajadas
        $totalHours = 0;
        foreach ($weeklyAssignments as $assignment) {
            if (method_exists($assignment, 'getShift')) {
                $previousShift = $assignment->getShift();
                $duration = $previousShift->getHorario()->getDurationInMinutes();
                $totalHours += $duration / 60;
            }
        }

        // Sumar la duración del nuevo turno
        $newDuration = $shift->getHorario()->getDurationInMinutes() / 60;
        $totalHours += $newDuration;

        if ($totalHours > 40) {
            throw new MaxHoursExceededException(
                "El empleado {$employee->getNombre()} excedería las 40 horas semanales. "
                . "Total con este turno: {$totalHours} horas."
            );
        }
    }

    
     // El empleado debe tener la habilidad requerida
     
    private function validateSkill(Employee $employee, Shift $shift): void
    {
        $requiredSkill = $shift->getHabilidad();
        
        if (!$employee->hasSkill($requiredSkill)) {
            throw new UnauthorizedSkillException(
                "El empleado {$employee->getNombre()} no tiene la habilidad requerida: "
                . $requiredSkill->value
            );
        }
    }

    
    //Priorizar a quien menos fines de semana ha trabajado
     
    private function validateFairness(Employee $employee, Shift $shift): void
    {
        // Si el turno no es de fin de semana, no aplica la regla
        if (!$this->isWeekend($shift->getHorario()->getStart())) {
            return;
        }
        if ($employee->getWeekendCount() > 5) {
            throw new UnfairDistributionException(
                "El empleado {$employee->getNombre()} ya ha trabajado {$employee->getWeekendCount()} "
                . "fines de semana. Hay otros empleados con menos."
            );
        }
    }

    /**
     * Verifica si una fecha es fin de semana
     */
    private function isWeekend(\DateTimeImmutable $date): bool
    {
        $dayOfWeek = (int) $date->format('N');
        return $dayOfWeek >= 6; 
    }
}