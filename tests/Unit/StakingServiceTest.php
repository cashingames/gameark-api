<?php

namespace Tests\Unit;

use App\Enums\FeatureFlags;
use App\Models\GameSession;
use App\Models\Staking;
use App\Models\User;
use App\Services\FeatureFlag;
use App\Services\StakingService;
use Database\Seeders\CategorySeeder;
use Database\Seeders\GameModeSeeder;
use Database\Seeders\GameTypeSeeder;
use Database\Seeders\PlanSeeder;
use Database\Seeders\StakingOddsRulesSeeder;
use Database\Seeders\StakingSeeder;
use Database\Seeders\TriviaSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StakingServiceTest extends TestCase
{
    use RefreshDatabase;

    public $stakingService;
    public $user, $staking;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->seed( StakingOddsRulesSeeder::class);
        $this->seed(StakingSeeder::class);
        $this->user = User::inRandomOrder()->first();
        $this->staking = Staking::inRandomOrder()->first();
        $this->stakingService = new StakingService($this->user);
        FeatureFlag::isEnabled(FeatureFlags::EXHIBITION_GAME_STAKING);
        FeatureFlag::isEnabled(FeatureFlags::TRIVIA_GAME_STAKING);
    }

    public function test_that_an_amount_can_be_staked()
    {
        $wallet = $this->user->wallet;
        $wallet->non_withdrawable_balance = 5000;
        $wallet->save();

        $stakingId = $this->stakingService->stakeAmount(1000);
        
        $this->assertIsInt($stakingId);
    }

    public function test_that_an_exhibition_staking_record_can_be_created()
    {   
        $this->seed(CategorySeeder::class);
        $this->seed(PlanSeeder::class);
        $this->seed(GameTypeSeeder::class);
        $this->seed(GameModeSeeder::class);
        GameSession::factory()->count(20)->create();

        $gameSession = GameSession::inRandomOrder()->first();

        $this->stakingService->createExhibitionStaking($this->staking->id, $gameSession->id);

        $this->assertDatabaseHas('exhibition_stakings', [
            'staking_id' => $this->staking->id,
            'game_session_id' => $gameSession->id,
        ]);
    }
}
