<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Bundle\FlowScheduler\Infrastructure\Persistence\Models\AssignmentModel;

class AssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $assignments = [
            [
                'id' => 'f6a7b8c9-d0e1-2345-def6-789012345678',
                'employee_id' => '550e8400-e29b-41d4-a716-446655440000',
                'shift_id' => 'a1b2c3d4-e5f6-7890-abcd-ef1234567890',
                'assigned_at' => '2025-01-20 07:30:00',
                'status' => 'confirmed',
            ],
            [
                'id' => 'a7b8c9d0-e1f2-3456-def7-890123456789',
                'employee_id' => '6ba7b810-9dad-11d1-80b4-00c04fd430c8',
                'shift_id' => 'b2c3d4e5-f6a7-8901-bcde-f23456789012',
                'assigned_at' => '2025-01-20 13:30:00',
                'status' => 'confirmed',
            ],
            [
                'id' => 'b8c9d0e1-f2a3-4567-def8-901234567890',
                'employee_id' => '8ba7b810-9dad-11d1-80b4-00c04fd430d0',
                'shift_id' => 'c3d4e5f6-a7b8-9012-cdef-345678901234',
                'assigned_at' => '2025-01-20 21:30:00',
                'status' => 'pending',
            ],
        ];

        foreach ($assignments as $assignment) {
            AssignmentModel::create($assignment);
        }
    }
}
