<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Board;
use App\Models\Todo;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardTodoTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_and_view_board(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('boards.store'), [
                'name' => 'Mi Pizarra de Prueba',
            ])
            ->assertRedirect();

        $board = Board::where('user_id', $user->id)->firstOrFail();
        $this->assertSame('Mi Pizarra de Prueba', $board->name);

        $this->actingAs($user)
            ->get(route('boards.show', $board->id))
            ->assertOk();
    }

    public function test_user_cannot_view_others_board(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $board = Board::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($stranger)
            ->get(route('boards.show', $board->id))
            ->assertForbidden();
    }

    public function test_todo_lifecycle(): void
    {
        $user = User::factory()->create();

        // Crear
        $this->actingAs($user)
            ->post(route('todos.store'), [
                'title' => 'Tarea de prueba',
                'priority' => 2,
            ])
            ->assertRedirect();

        $todo = Todo::where('user_id', $user->id)->firstOrFail();
        $this->assertSame('Tarea de prueba', $todo->title);

        // Toggle
        $this->actingAs($user)
            ->patch(route('todos.toggle', $todo->id))
            ->assertOk();

        $this->assertTrue($todo->fresh()->completed);

        // Borrar
        $this->actingAs($user)
            ->delete(route('todos.destroy', $todo->id))
            ->assertOk();

        $this->assertDatabaseMissing('todos', ['id' => $todo->id]);
    }

    public function test_user_cannot_modify_others_todo(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($stranger)
            ->patch(route('todos.toggle', $todo->id))
            ->assertForbidden();
    }
}
