<?php

namespace Tests\Feature;

use App\Models\Board;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardContentCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_board_content_uses_drawable_canvas_format(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/boards', [
                'name' => 'Tablero de prueba',
            ]);

        $board = Board::query()->where('user_id', $user->id)->latest()->firstOrFail();

        $this->assertSame('board', $board->content['type'] ?? null);
        $this->assertArrayHasKey('strokes', $board->content);
        $this->assertArrayHasKey('tables', $board->content);
        $this->assertArrayHasKey('viewport', $board->content);
        $this->assertSame(2, $board->content['version'] ?? null);
    }

    public function test_user_cannot_create_board_for_another_team(): void
    {
        $owner = User::factory()->create();
        $user = User::factory()->create();
        $team = Team::create([
            'name' => 'Equipo ajeno',
            'description' => null,
            'invitation_code' => 'ABC-1234-XYZ',
            'owner_id' => $owner->id,
        ]);

        $this->actingAs($user)
            ->post('/boards', [
                'name' => 'Pizarra no autorizada',
                'team_id' => $team->id,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('boards', [
            'name' => 'Pizarra no autorizada',
        ]);
    }

    public function test_team_owner_controls_employee_editing_and_members_receive_content(): void
    {
        $owner = User::factory()->create(['role' => 'boss']);
        $employee = User::factory()->create(['role' => 'employee']);
        $team = Team::create([
            'name' => 'Equipo colaborativo',
            'description' => null,
            'invitation_code' => 'LIVE-1234-TEAM',
            'owner_id' => $owner->id,
        ]);
        $team->users()->attach($owner->id, ['role' => 'admin']);
        $team->users()->attach($employee->id, ['role' => 'member']);
        $board = Board::create([
            'name' => 'Pizarra en directo',
            'team_id' => $team->id,
            'user_id' => $owner->id,
            'content' => ['type' => 'board', 'strokes' => []],
        ]);

        $payload = [
            'type' => 'board',
            'strokes' => [['id' => 'remote-stroke', 'points' => [['x' => 1, 'y' => 2]]]],
            'collaboration' => ['allowEmployeeEdit' => false],
        ];

        $this->actingAs($employee)
            ->putJson(route('boards.update', $board), ['content' => json_encode($payload)])
            ->assertForbidden();

        $payload['collaboration']['allowEmployeeEdit'] = true;
        $this->actingAs($owner)
            ->putJson(route('boards.update', $board), ['content' => json_encode($payload)])
            ->assertOk();

        $this->actingAs($employee)
            ->getJson(route('boards.content', $board))
            ->assertOk()
            ->assertJsonPath('content.collaboration.allowEmployeeEdit', true)
            ->assertJsonPath('content.strokes.0.id', 'remote-stroke');

        $board->refresh();
        $employeePayload = [...$payload, 'strokes' => []];
        $this->actingAs($employee)
            ->putJson(route('boards.update', $board), ['content' => json_encode($employeePayload)])
            ->assertOk();
    }
}
