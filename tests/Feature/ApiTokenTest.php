<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_api_token(): void
    {
        $user = User::factory()->create([
            'email' => 'api@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/auth/token', [
            'email' => 'api@example.com',
            'password' => 'password',
            'device_name' => 'test-device',
        ]);

        $response->assertCreated()->assertJsonStructure([
            'token',
            'user' => ['id', 'name', 'email', 'role'],
        ]);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_invalid_api_credentials_are_rejected(): void
    {
        User::factory()->create([
            'email' => 'api@example.com',
            'password' => 'password',
        ]);

        $this->postJson('/api/auth/token', [
            'email' => 'api@example.com',
            'password' => 'wrong-password',
        ])
            ->assertUnprocessable()
            ->assertJson(['error' => 'Las credenciales no son válidas']);
    }

    public function test_user_can_revoke_current_api_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-device')->plainTextToken;

        $this->withToken($token)
            ->deleteJson('/api/auth/token')
            ->assertOk()
            ->assertJson(['message' => 'Token revocado correctamente']);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
