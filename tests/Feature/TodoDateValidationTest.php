<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoDateValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_todo_cannot_be_created_for_today_or_earlier(): void
    {
        $user = User::factory()->create(['onboarding_completed' => true]);

        $this->actingAs($user)
            ->postJson(route('todos.store'), [
                'title' => 'Tarea inválida',
                'due_date' => now()->toDateString(),
                'priority' => 1,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('due_date');

        $this->assertDatabaseMissing('todos', ['title' => 'Tarea inválida']);
    }

    public function test_todo_can_be_created_for_a_future_date(): void
    {
        $user = User::factory()->create(['onboarding_completed' => true]);
        $futureDate = now()->addDay()->toDateString();

        $this->actingAs($user)
            ->postJson(route('todos.store'), [
                'title' => 'Tarea futura',
                'due_date' => $futureDate,
                'priority' => 3,
            ])
            ->assertCreated();

        $this->assertDatabaseHas('todos', [
            'title' => 'Tarea futura',
            'due_date' => $futureDate . ' 00:00:00',
            'priority' => 3,
        ]);
    }

    public function test_todo_cannot_be_modified_to_a_past_date(): void
    {
        $user = User::factory()->create(['onboarding_completed' => true]);
        $todo = Todo::create([
            'title' => 'Tarea editable',
            'due_date' => now()->addDays(3),
            'priority' => 2,
            'user_id' => $user->id,
            'completed' => false,
        ]);

        $this->actingAs($user)
            ->putJson(route('todos.update', $todo), [
                'title' => 'Tarea editable',
                'due_date' => now()->toDateString(),
                'priority' => 2,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('due_date');
    }
}
