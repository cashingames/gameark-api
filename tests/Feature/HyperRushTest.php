<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\HyperRushGameSession;
use App\Models\Question;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HyperRushTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $category;

    const START_HYPER_RUSH_GAME_URL = '/api/v3/hyper-rush';
    const END_HYPER_RUSH_GAME_URL = '/api/v3/game/end/hyper-rush';
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(UserSeeder::class);
        $this->seed(CategorySeeder::class);
        // $this->seed(GameTypeSeeder::class);

        $this->user = User::first();
        $this->category = Category::where('name', 'Hyper Trivia')->first();
        $this->actingAs($this->user);
    }

    public function test_hyper_rush_game_can_be_started()
    {
        $questions = Question::factory()
            ->count(250)
            ->state(
                new Sequence(
                    ['level' => 'easy']
                )
            )
            ->create();

        $data = [];

        foreach ($questions as $question) {
            $data[] = [
                'question_id' => $question->id,
                'category_id' => $this->category->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('categories_questions')->insert($data);

        $response = $this->postjson(self::START_HYPER_RUSH_GAME_URL, [
            "category" => $this->category->name
        ]);
        $response->assertOk();
    }


    
    public function test_hyper_rush_game_can_not_be_started_with_the_wrong_category()
    {
        $questions = Question::factory()
            ->count(250)
            ->state(
                new Sequence(
                    ['level' => 'easy']
                )
            )
            ->create();

        $data = [];

        foreach ($questions as $question) {
            $data[] = [
                'question_id' => $question->id,
                'category_id' => $this->category->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('categories_questions')->insert($data);

        $response = $this->postjson(self::START_HYPER_RUSH_GAME_URL, [
            "category" => 'Music lao'
        ]);
        $response->assertJson([
            'message' => 'Category not available for now, try again later',
        ]);
    }


    public function test_hyper_rush_game_can_be_ended()
    {
        $questions = Question::factory()
        ->count(250)
        ->state(
            new Sequence(
                ['level' => 'easy'],
            )
        )
        ->create();

    $data = [];

    foreach ($questions as $question) {
        $data[] = [
            'question_id' => $question->id,
            'category_id' => $this->category->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    DB::table('categories_questions')->insert($data);

    $this->postjson(self::START_HYPER_RUSH_GAME_URL, [
        "category" => $this->category->name
    ]);
    
        HyperRushGameSession::where('user_id', '!=', $this->user->id)->update(['user_id' => $this->user->id]);
        $game = $this->user->hyperRushGameSessions()->first();
        $game->update(['state' => 'ONGOING']);

        $response = $this->postjson(self::END_HYPER_RUSH_GAME_URL, [
            "token" => $game->session_token,
            "chosenOptions" => [],
        ]);
        $response->assertJson([
            'message' => 'Game Ended',
        ]);
    }
}
