<?php

namespace Database\Factories;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TodoFactory extends Factory
{
    protected $model = Todo::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph,
            'due_date' => now()->addDays(rand(1, 7)),
            'priority' => rand(1, 3),
            'user_id' => User::factory(),
            'completed' => false,
        ];
    }
}
