<?php
namespace App\Bundle\FlowScheduler\Domain\Ports;

use App\Bundle\FlowScheduler\Domain\Entities\Shift;
use App\Bundle\FlowScheduler\Domain\ValueObjects\VOBShiftId;


interface ShiftRepositoryInterface
{
    public function save(Shift $shift): void;
    public function findById(VOBShiftId $id): ?Shift;
    public function findAll(): array;
    public function findByDateRange(\DateTimeImmutable $start, \DateTimeImmutable $end): array;
}