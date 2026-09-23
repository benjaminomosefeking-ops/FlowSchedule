<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimulationTest extends TestCase
{
    use RefreshDatabase;

    public function test_boss_complete_user_journey(): void
    {
        // 1. Registration
        $this->post('/register', [
            'name' => 'Boss User',
            'email' => 'boss@flow.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/dashboard');

        // 2. Onboarding as Boss
        $this->post('/onboarding', [
            'role' => 'boss',
            'company_name' => 'Innovate Corp',
            'company_description' => 'Leading the flow of work',
        ])->assertRedirect('/dashboard');

        $user = User::where('email', 'boss@flow.com')->first();
        $user->markEmailAsVerified();
        $this->actingAs($user);

        // 3. Dashboard
        $response = $this->get('/dashboard');
        $response->assertStatus(200);

        // 4. Boards
        $team = $user->ownedTeams()->first();
        $this->post('/boards', [
            'name' => 'Project Alpha',
            'team_id' => $team->id,
        ])->assertRedirect();

        $board = \App\Models\Board::where('name', 'Project Alpha')->first();
        $this->get('/boards/' . $board->id)->assertStatus(200);

        // 5. Todos
        $this->post('/todos', [
            'title' => 'Setup Team',
            'description' => 'First important task',
            'priority' => 1,
        ])->assertRedirect();

        // 6. Calendar
        $date = now()->format('Y-m-d');
        $this->put("/calendar/events/{$date}", [
            'title' => 'Team Meeting',
            'note' => 'Sync on Project Alpha',
            'color' => '#FF0000',
        ])->assertStatus(200);

        // 7. Profile
        $this->patch('/profile', [
            'name' => 'Boss User Updated',
            'email' => 'boss@flow.com',
        ])->assertRedirect('/profile');

        // 8. Logout
        $this->post('/logout')->assertRedirect('/');
    }

    public function test_employee_complete_user_journey(): void
    {
        $boss = User::factory()->create(['role' => 'boss']);
        $team = Team::factory()->create([
            'owner_id' => $boss->id,
            'invitation_code' => 'JOIN-1234-NOW',
        ]);

        // 1. Registration
        $this->post('/register', [
            'name' => 'Employee User',
            'email' => 'emp@flow.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/dashboard');

        // 2. Onboarding as Employee
        $this->post('/onboarding', [
            'role' => 'employee',
            'team_code' => 'JOIN-1234-NOW',
        ])->assertRedirect('/dashboard');

        $user = User::where('email', 'emp@flow.com')->first();
        $user->markEmailAsVerified(); // Mark as verified to pass middleware
        $this->actingAs($user);

        // 3. Dashboard
        $response = $this->get('/dashboard');
        if ($response->status() === 302) {
            fwrite(STDERR, 'Employee Dashboard Redirect: ' . $response->headers->get('Location') . PHP_EOL);
        }
        $response->assertStatus(200);

        // 4. Teams
        $this->get('/teams')->assertStatus(200);

        // 5. Boards
        $board = \App\Models\Board::create([
            'name' => 'Team Board',
            'user_id' => $boss->id,
            'team_id' => $team->id,
            'content' => [],
        ]);
        $this->get('/boards/' . $board->id)->assertStatus(200);

        // 6. Logout
        $this->post('/logout')->assertRedirect('/');
    }
}
