<?php

namespace Database\Factories;

use App\Models\GameMode;
use App\Models\GameSession;
use App\Models\GameType;
use App\Models\Plan;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Sequence;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GameSession>
 */
class GameSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = GameSession::class;

    public function definition()
    {
        return [
            //
            'user_id' => User::factory(),
            'plan_id' => Plan::factory(),
            'game_mode_id' => GameMode::factory(),
            'game_type_id' => GameType::factory(),
            'category_id' => Category::factory(),
            'start_time' => Carbon::now(),
            'end_time' => Carbon::now()->addMinutes(1),
            'session_token' => Str::random(20),
            // 'trivia_id' => $this->faker->randomElement(array(1,2,3,4,5)),
            'state' => 'COMPLETED',
            'correct_count' => $this->faker->randomElement(array(1,2,3,4,5,6,7,8,9,10)),
            'wrong_count' => $this->faker->randomElement(array(1,2,3,4,5,6,7,8,9,10)),
            'total_count' =>10,
            'points_gained' => $this->faker->randomElement(array(5,10,15,20)),
            'created_at' => Carbon::today()->subDays(2),
            'updated_at' => Carbon::now()
        ];
    }
}