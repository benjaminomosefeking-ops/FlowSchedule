<?php

namespace App\Bundle\FlowScheduler\Infrastructure\Persistence\Repositories;

use App\Bundle\FlowScheduler\Domain\Entities\Shift;
use App\Bundle\FlowScheduler\Domain\Ports\ShiftRepositoryInterface;
use App\Bundle\FlowScheduler\Domain\ValueObjects\VOBShiftId;
use App\Bundle\FlowScheduler\Domain\ValueObjects\VOBTimeSlot;
use App\Bundle\FlowScheduler\Domain\Enums\ShiftType;
use App\Bundle\FlowScheduler\Domain\Enums\Skill;
use App\Bundle\FlowScheduler\Domain\Enums\AssignmentStatus;
use App\Bundle\FlowScheduler\Infrastructure\Persistence\Models\ShiftModel;

class EloquentShiftRepository implements ShiftRepositoryInterface
{
    public function save(Shift $shift): void
    {
        ShiftModel::updateOrCreate(
            ['id' => $shift->getId()->getNombre()],
            [
                'title' => $shift->getTitulo(),
                'description' => $shift->getDescripcion(),
                'type' => $shift->getTipo()->value,
                'required_skill' => $shift->getHabilidad()->value,
                'start_time' => $shift->getHorario()->getStart(),
                'end_time' => $shift->getHorario()->getEnd(),
                'status' => $shift->getEstado()->value,
                'team_id' => $shift->getTeamId(),
            ]
        );
    }

    public function findById(VOBShiftId $id): ?Shift
    {
        $model = ShiftModel::find($id->getNombre());
        if (!$model) {
            return null;
        }
        return $this->mapToDomain($model);
    }

    public function findAll(): array
    {
        $models = ShiftModel::all();
        return $models->map(fn($model) => $this->mapToDomain($model))->toArray();
    }

    public function findByDateRange(\DateTimeImmutable $start, \DateTimeImmutable $end): array
    {
        $models = ShiftModel::where('start_time', '>=', $start)
            ->where('end_time', '<=', $end)
            ->get();
        return $models->map(fn($model) => $this->mapToDomain($model))->toArray();
    }

    private function mapToDomain(ShiftModel $model): Shift
    {
        $timeSlot = new VOBTimeSlot(
            new \DateTimeImmutable($model->start_time),
            new \DateTimeImmutable($model->end_time)
        );

        return new Shift(
            new VOBShiftId($model->id),
            $model->title,
            $model->description,
            ShiftType::from($model->type),
            Skill::from($model->required_skill),
            $timeSlot,
            AssignmentStatus::from($model->status),
            $model->team_id
        );
    }
}