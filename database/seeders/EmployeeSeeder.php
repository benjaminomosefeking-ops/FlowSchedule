<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Bundle\FlowScheduler\Infrastructure\Persistence\Models\EmployeeModel;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $employees = [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'nombre' => 'Ana García',
                'email' => 'ana.garcia@hospital.com',
                'skills' => json_encode(['nurse', 'xray']),
                'weekend_count' => 2,
            ],
            [
                'id' => '6ba7b810-9dad-11d1-80b4-00c04fd430c8',
                'nombre' => 'Carlos Ruiz',
                'email' => 'carlos.ruiz@hospital.com',
                'skills' => json_encode(['doctor', 'xray']),
                'weekend_count' => 1,
            ],
            [
                'id' => '7ba7b810-9dad-11d1-80b4-00c04fd430c9',
                'nombre' => 'María López',
                'email' => 'maria.lopez@hospital.com',
                'skills' => json_encode(['nurse']),
                'weekend_count' => 3,
            ],
            [
                'id' => '8ba7b810-9dad-11d1-80b4-00c04fd430d0',
                'nombre' => 'Juan Pérez',
                'email' => 'juan.perez@hospital.com',
                'skills' => json_encode(['doctor']),
                'weekend_count' => 0,
            ],
            [
                'id' => '9ba7b810-9dad-11d1-80b4-00c04fd430d1',
                'nombre' => 'Laura Sánchez',
                'email' => 'laura.sanchez@hospital.com',
                'skills' => json_encode(['nurse']),
                'weekend_count' => 4,
            ],
        ];

        foreach ($employees as $employee) {
            EmployeeModel::create($employee);
        }
    }
}
