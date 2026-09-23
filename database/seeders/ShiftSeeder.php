<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Bundle\FlowScheduler\Infrastructure\Persistence\Models\ShiftModel;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            [
                'id' => 'a1b2c3d4-e5f6-7890-abcd-ef1234567890',
                'title' => 'Turno de Mañana - Enfermería',
                'description' => 'Atención a pacientes en el área de enfermería',
                'type' => 'morning',
                'required_skill' => 'nurse',
                'start_time' => '2025-01-20 08:00:00',
                'end_time' => '2025-01-20 15:00:00',
                'status' => 'pending',
            ],
            [
                'id' => 'b2c3d4e5-f6a7-8901-bcde-f23456789012',
                'title' => 'Turno de Tarde - Radiología',
                'description' => 'Atención de pacientes para rayos X',
                'type' => 'afternoon',
                'required_skill' => 'xray',
                'start_time' => '2025-01-20 14:00:00',
                'end_time' => '2025-01-20 21:00:00',
                'status' => 'pending',
            ],
            [
                'id' => 'c3d4e5f6-a7b8-9012-cdef-345678901234',
                'title' => 'Turno de Noche - Urgencias',
                'description' => 'Atención en el área de urgencias',
                'type' => 'night',
                'required_skill' => 'doctor',
                'start_time' => '2025-01-20 22:00:00',
                'end_time' => '2025-01-21 06:00:00',
                'status' => 'pending',
            ],
            [
                'id' => 'd4e5f6a7-b8c9-0123-cdef-456789012345',
                'title' => 'Turno de Mañana - Medicina General',
                'description' => 'Consultas de medicina general',
                'type' => 'morning',
                'required_skill' => 'doctor',
                'start_time' => '2025-01-21 08:00:00',
                'end_time' => '2025-01-21 15:00:00',
                'status' => 'pending',
            ],
            [
                'id' => 'e5f6a7b8-c9d0-1234-def5-678901234567',
                'title' => 'Turno de Tarde - Enfermería',
                'description' => 'Atención a pacientes en enfermería',
                'type' => 'afternoon',
                'required_skill' => 'nurse',
                'start_time' => '2025-01-21 14:00:00',
                'end_time' => '2025-01-21 21:00:00',
                'status' => 'confirmed',
            ],
        ];

        foreach ($shifts as $shift) {
            ShiftModel::create($shift);
        }
    }
}
