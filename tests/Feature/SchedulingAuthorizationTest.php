<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Team;
use App\Bundle\FlowScheduler\Infrastructure\Persistence\Models\EmployeeModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchedulingAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_cannot_create_shift_through_scheduling_api(): void
    {
        $employee = User::factory()->create([
            'role' => 'employee',
            'onboarding_completed' => true,
        ]);

        $this->actingAs($employee)
            ->postJson('/api/scheduling/shifts', [
                'title' => 'Turno de prueba',
                'description' => 'No autorizado',
                'type' => 'morning',
                'required_skill' => 'nurse',
                'start_time' => '2026-09-07 08:00:00',
                'end_time' => '2026-09-07 16:00:00',
            ])
            ->assertForbidden()
            ->assertJson(['error' => 'Solo un jefe puede crear turnos']);
    }

    public function test_boss_can_create_shift_only_for_owned_team(): void
    {
        $boss = User::factory()->create([
            'role' => 'boss',
            'onboarding_completed' => true,
        ]);
        $otherBoss = User::factory()->create([
            'role' => 'boss',
            'onboarding_completed' => true,
        ]);
        $team = Team::create([
            'name' => 'Equipo propio',
            'description' => null,
            'invitation_code' => 'OWN-1234-XYZ',
            'owner_id' => $boss->id,
        ]);
        $otherTeam = Team::create([
            'name' => 'Equipo ajeno',
            'description' => null,
            'invitation_code' => 'OTH-1234-XYZ',
            'owner_id' => $otherBoss->id,
        ]);

        $payload = [
            'title' => 'Turno de prueba',
            'description' => 'Turno del equipo',
            'type' => 'morning',
            'required_skill' => 'nurse',
            'start_time' => '2026-09-07 08:00:00',
            'end_time' => '2026-09-07 16:00:00',
        ];

        $this->actingAs($boss)
            ->postJson('/api/scheduling/shifts', [...$payload, 'team_id' => $team->id])
            ->assertCreated();

        $this->actingAs($boss)
            ->postJson('/api/scheduling/shifts', [...$payload, 'team_id' => $otherTeam->id])
            ->assertForbidden();
    }

    public function test_assignment_requires_employee_from_shift_team(): void
    {
        $boss = User::factory()->create(['role' => 'boss', 'onboarding_completed' => true]);
        $otherBoss = User::factory()->create(['role' => 'boss', 'onboarding_completed' => true]);
        $team = Team::create([
            'name' => 'Equipo propio',
            'description' => null,
            'invitation_code' => 'ASN-1234-XYZ',
            'owner_id' => $boss->id,
        ]);
        $otherTeam = Team::create([
            'name' => 'Equipo ajeno',
            'description' => null,
            'invitation_code' => 'ASN-5678-XYZ',
            'owner_id' => $otherBoss->id,
        ]);

        $employee = EmployeeModel::create([
            'nombre' => 'Empleado propio',
            'email' => 'propio@example.com',
            'team_id' => $team->id,
            'skills' => ['nurse'],
            'weekend_count' => 0,
        ]);
        $otherEmployee = EmployeeModel::create([
            'nombre' => 'Empleado ajeno',
            'email' => 'ajeno@example.com',
            'team_id' => $otherTeam->id,
            'skills' => ['nurse'],
            'weekend_count' => 0,
        ]);

        $shiftResponse = $this->actingAs($boss)->postJson('/api/scheduling/shifts', [
            'title' => 'Turno asignable',
            'description' => 'Turno del equipo',
            'type' => 'morning',
            'required_skill' => 'nurse',
            'team_id' => $team->id,
            'start_time' => '2026-09-07 08:00:00',
            'end_time' => '2026-09-07 16:00:00',
        ])->assertCreated();

        $shiftId = $shiftResponse->json('data.id');

        $this->actingAs($boss)
            ->postJson('/api/scheduling/assign', [
                'employee_id' => $employee->id,
                'shift_id' => $shiftId,
            ])
            ->assertCreated();

        $this->actingAs($boss)
            ->postJson('/api/scheduling/assign', [
                'employee_id' => $otherEmployee->id,
                'shift_id' => $shiftId,
            ])
            ->assertForbidden();
    }
}
