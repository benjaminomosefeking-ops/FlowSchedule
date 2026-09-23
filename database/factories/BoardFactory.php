<?php

namespace Database\Factories;

use App\Models\Board;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoardFactory extends Factory
{
    protected $model = Board::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'user_id' => User::factory(),
            'team_id' => null,
            'content' => json_encode([
                'type' => 'board',
                'version' => 2,
                'strokes' => [],
                'tables' => [],
                'viewport' => ['scale' => 1, 'offsetX' => 0, 'offsetY' => 0],
            ]),
        ];
    }
}
