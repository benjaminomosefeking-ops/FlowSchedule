<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_is_redirected_to_onboarding_if_not_completed(): void
    {
        $user = User::factory()->create([
            'onboarding_completed' => false,
        ]);

        $this->actingAs($user);

        $response = $this->get('/onboarding');

        $response->assertStatus(200);
    }

    public function test_user_is_redirected_to_dashboard_if_onboarding_completed(): void
    {
        $user = User::factory()->create([
            'onboarding_completed' => true,
        ]);

        $this->actingAs($user);

        $response = $this->get('/onboarding');

        $response->assertRedirect(route('dashboard'));
    }

    public function test_boss_can_complete_onboarding_and_create_team(): void
    {
        $user = User::factory()->create([
            'onboarding_completed' => false,
            'role' => 'employee', // Start as employee to change to boss in onboarding
        ]);

        $this->actingAs($user);

        $response = $this->post('/onboarding', [
            'role' => 'boss',
            'company_name' => 'Test Company',
            'company_description' => 'A test company description',
        ]);

        $this->assertEquals('boss', $user->fresh()->role);
        $this->assertTrue($user->fresh()->onboarding_completed);

        $this->assertDatabaseHas('teams', [
            'name' => 'Test Company',
            'owner_id' => $user->id,
        ]);

        $this->assertDatabaseHas('team_user', [
            'user_id' => $user->id,
        ]);

        $response->assertRedirect(route('dashboard'));
    }

    public function test_employee_can_complete_onboarding_and_join_team(): void
    {
        $team = Team::factory()->create([
            'invitation_code' => 'TEST-1234-CODE',
        ]);

        $user = User::factory()->create([
            'onboarding_completed' => false,
            'role' => 'employee',
        ]);

        $this->actingAs($user);

        $response = $this->post('/onboarding', [
            'role' => 'employee',
            'team_code' => 'TEST-1234-CODE',
        ]);

        $this->assertTrue($user->fresh()->onboarding_completed);
        $this->assertDatabaseHas('team_user', [
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);

        $response->assertRedirect(route('dashboard'));
    }

    public function test_employee_cannot_join_with_invalid_code(): void
    {
        $user = User::factory()->create([
            'onboarding_completed' => false,
            'role' => 'employee',
        ]);

        $this->actingAs($user);

        $response = $this->post('/onboarding', [
            'role' => 'employee',
            'team_code' => 'INVALID-CODE',
        ]);

        $this->assertFalse($user->fresh()->onboarding_completed);
        $response->assertSessionHasErrors('team_code');
    }
}
