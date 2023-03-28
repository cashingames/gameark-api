<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Question;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;
use App\Services\Firebase\FirestoreService;

class StartChallengeRequestTest extends TestCase
{
    use RefreshDatabase;
    const API_URL = '/api/v3/challenges/create';

    public function setUp(): void
    {
        parent::setUp();
        // $this->withoutExceptionHandling();

        $this->instance(
            FirestoreClient::class,
            Mockery::mock(FirestoreClient::class)
        );
    }

    public function test_challenge_request_returns_sucess(): void
    {
        $this->instance(
            FirestoreService::class,
            Mockery::mock(FirestoreService::class, function (MockInterface $mock) {
                $mock->shouldReceive('createDocument')->once();
            })
        );

        $user = User::factory()->create();
        $category = Category::factory()->create();

        Wallet::factory()
            ->for($user)
            ->create([
                'non_withdrawable_balance' => 1000
            ]);

        $response = $this->actingAs($user)
            ->post(self::API_URL, [
                'category' => $category->id,
                'amount' => 500
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('challenge_requests', [
            'category_id' => $category->id,
            'amount' => 500,
            'user_id' => $user->id,
            'username' => $user->username
        ]);

        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
            'non_withdrawable_balance' => 500,
        ]);

        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $user->wallet->id,
            'amount' => 500,
        ]);
    }

    public function test_challenge_request_returns_error_when_user_has_insufficient_balance(): void
    {
        $this->instance(
            FirestoreService::class,
            Mockery::mock(FirestoreService::class)
        );

        $this->instance(
            StakingChallengeGameService::class,
            Mockery::mock(StakingChallengeGameService::class, function (MockInterface $mock) {
                $mock->shouldReceive('createDocument')->never();
            })
        );


        $user = User::factory()->create();
        $category = Category::factory()->create();

        Wallet::factory()
            ->for($user)
            ->create([
                'non_withdrawable_balance' => 0
            ]);

        $response = $this->actingAs($user)
            ->postJson(self::API_URL, [
                'category' => $category->id,
                'amount' => 1000
            ]);

        $response->assertStatus(422);
    }

    public function test_challenge_request_found_match()
    {
        $this->instance(
            FirestoreService::class,
            Mockery::mock(FirestoreService::class, function (MockInterface $mock) {
                $mock->shouldReceive('createDocument')->times(2);
                $mock->shouldReceive('updateDocument')->times(2);
            })
        );

        $category = Category::factory()->create();
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

        $this->prepareMatchRequest($category, 500);
        $this->prepareMatchRequest($category, 500);

        $this->assertDatabaseEmpty('challenge_requests');

    }
    private function prepareMatchRequest($category, $amount): void
    {
        $user = User::factory()->create();
        Wallet::factory()
            ->for($user)
            ->create([
                'non_withdrawable_balance' => 1000
            ]);
        $this->actingAs($user)
            ->post(self::API_URL, [
                'category' => $category->id,
                'amount' => $amount
            ]);
    }
}