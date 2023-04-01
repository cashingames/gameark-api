<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ChallengeRequest;
use App\Models\Option;
use App\Models\Question;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;
use App\Services\Firebase\FirestoreService;


class EndChallengeTest extends TestCase
{

    use RefreshDatabase;

    const API_URL = '/api/v3/challenges/submit';

    public function setUp(): void
    {
        parent::setUp();

        $this->instance(
            FirestoreClient::class,
            Mockery::mock(FirestoreClient::class, function (MockInterface $mock) {
                $mock->shouldReceive('createDocument')->never();
            })
        );
    }

    public function test_challenge_draw_flow(): void
    {
        $this->instance(
            FirestoreService::class,
            Mockery::mock(FirestoreService::class, function (MockInterface $mock) {
                $mock->shouldReceive('updateDocument')->times(4);
            })
        );

        $category = Category::factory()->create();
        $this->seedQuestions($category);

        $firstUser = User::factory()->has(Wallet::factory())->create();
        $secondUser = User::factory()->has(Wallet::factory())->create();

        ChallengeRequest::factory()->for($firstUser)->create([
            'session_token' => '123',
            'challenge_request_id' => '1',
            'status' => 'MATCHED',
            'category_id' => $category->id,
            'amount' => 500,
            'started_at' => now(),
        ]);

        ChallengeRequest::factory()->for($secondUser)->create([
            'session_token' => '123',
            'challenge_request_id' => '2',
            'status' => 'MATCHED',
            'category_id' => $category->id,
            'amount' => 500,
            'started_at' => now()
        ]);

        $this
            ->actingAs(User::first())
            ->postJson(
                self::API_URL,
                [
                    'challenge_request_id' => '1',
                    'selected_options' => [
                        [
                            'question_id' => 1,
                            'option_id' => 1
                        ]
                    ]
                ]
            )
            ->assertStatus(200);

        $this->assertDatabaseHas('challenge_requests', [
            'challenge_request_id' => '1',
            'status' => 'COMPLETED',
        ]);
        $this->assertDatabaseHas('challenge_requests', [
            'challenge_request_id' => '2',
            'status' => 'MATCHED',
        ]);

        $this
            ->actingAs(User::find(2))
            ->postJson(
                self::API_URL,
                [
                    'challenge_request_id' => '2',
                    'selected_options' => [
                        [
                            'question_id' => '1',
                            'option_id' => '1'
                        ],
                        [
                            'question_id' => '1',
                            'option_id' => '1'
                        ],

                    ]
                ]
            )
            ->assertStatus(200);

        $this->assertDatabaseHas('challenge_requests', [
            'challenge_request_id' => '1',
            'status' => 'COMPLETED',
        ]);
        $this->assertDatabaseHas('challenge_requests', [
            'challenge_request_id' => '2',
            'status' => 'COMPLETED',
        ]);

        //refund if both users got the same score
        $this->assertDatabaseHas('wallets', [
            'user_id' => 1,
            'non_withdrawable_balance' => 500,
        ]);
        $this->assertDatabaseHas('wallets', [
            'user_id' => 2,
            'non_withdrawable_balance' => 500,
        ]);
    }

    private function seedQuestions(Category $category): array
    {
        $questions = Question::factory()
            ->hasOptions(4)
            ->count(250)
            ->create();

        $data = [];

        foreach ($questions as $question) {
            $data[] = [
                'question_id' => $question->id,
                'category_id' => $category->id
            ];
        }

        DB::table('categories_questions')->insert($data);

        return $data;
    }

}
