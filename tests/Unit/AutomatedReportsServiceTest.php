<?php

namespace Tests\Unit;

use App\Models\ExhibitionStaking;
use App\Models\GameSession;
use App\Models\Staking;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Repositories\Cashingames\ChallengeReportsRepository;
use App\Services\AutomatedReportsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutomatedReportsServiceTest extends TestCase
{
    use RefreshDatabase;

    public $reportsService;
    public $user;

    protected function setUp(): void
    {
        parent::setUp();
        User::factory()
            ->count(10)
            ->create(['created_at' => now('Africa/Lagos')->yesterday()]);
        WalletTransaction::factory()
            ->count(10)
            ->create(['created_at' => now('Africa/Lagos')->yesterday()]);
        Staking::factory()
            ->count(10)
            ->create(['created_at' => now('Africa/Lagos')->yesterday()]);
        GameSession::factory()
            ->count(10)
            ->create(['created_at' => now('Africa/Lagos')->yesterday()]);

        $this->user = User::inRandomOrder()->first();
        $challengeRepository = new ChallengeReportsRepository();
        $this->reportsService = new AutomatedReportsService( $challengeRepository );
    }

    public function test_that_daily_reports_returns_data()
    {
        $dailyReports = $this->reportsService->getDailyReports();
        $this->assertCount(16, $dailyReports);
    }

    public function test_that_daily_reports_returns_bogus_net_profit()
    {
        $dailyReports = $this->reportsService->getDailyReports();
        $this->assertArrayHasKey('bogusNetProfit', $dailyReports);
    }
    public function test_that_daily_reports_returns_true_net_profit()
    {
        $dailyReports = $this->reportsService->getDailyReports();
        $this->assertArrayHasKey('trueNetProfit', $dailyReports);
    }

    public function test_that_daily_reports_correct_total_withdrawal_amount()
    {
        WalletTransaction::query()->update([
            'transaction_type' => 'DEBIT',
            'description' => 'Winnings Withdrawal Made',
            'created_at' => now('Africa/Lagos')->subDays(1),
            'amount' => 100
        ]);

        $dailyReports = $this->reportsService->getDailyReports();

        $this->assertEquals('1,000', $dailyReports['totalWithdrawals']);
    }

    public function test_that_daily_reports_correct_total_fundings_amount()
    {
        WalletTransaction::query()->update([
            'transaction_type' => 'CREDIT',
            'description' => 'Fund Wallet',
            'created_at' => now('Africa/Lagos')->subDays(1),
            'amount' => 100
        ]);

        $dailyReports = $this->reportsService->getDailyReports();
        $this->assertEquals('1,000', $dailyReports['totalFundedAmount']);
    }

    public function test_that_daily_reports_correct_total_staked_amount()
    {
        Staking::query()->update([
            'created_at' => now('Africa/Lagos')->subDays(1),
            'amount_staked' => 100
        ]);

        $dailyReports = $this->reportsService->getDailyReports();
        $this->assertEquals('1,000', $dailyReports['totalStakedAmount']);
    }

    public function test_that_daily_reports_correct_total_amount_won()
    {
        Staking::query()->update([
            'created_at' => now('Africa/Lagos')->subDays(1),
            'amount_won' => 100
        ]);

        $dailyReports = $this->reportsService->getDailyReports();
        $this->assertEquals('1,000', $dailyReports['totalAmountWon']);
    }

    public function test_that_weekly_reports_returns_data()
    {
        $weeklyReports = $this->reportsService->getWeeklyReports();
        $this->assertCount(22, $weeklyReports);
    }

    public function test_that_weekly_reports_correct_total_amount_won()
    {
        Staking::query()->update([
            'created_at' => now('Africa/Lagos')->subDays(1),
            'amount_won' => 200
        ]);

        $weeklyReports = $this->reportsService->getWeeklyReports();
        $this->assertEquals('2,000', $weeklyReports['totalAmountWon']);
    }

    public function test_that_weekly_reports_correct_total_staked_amount()
    {
        Staking::query()->update([
            'created_at' => now('Africa/Lagos')->subDays(1),
            'amount_staked' => 400
        ]);

        $weeklyReports = $this->reportsService->getWeeklyReports();
        $this->assertEquals('4,000', $weeklyReports['totalStakedamount']);
    }

    public function test_that_weekly_reports_correct_total_withdrawal_amount()
    {
        WalletTransaction::query()->update([
            'transaction_type' => 'DEBIT',
            'description' => 'Winnings Withdrawal Made',
            'created_at' => now('Africa/Lagos')->subDays(1),
            'amount' => 100
        ]);

        $weeklyReports = $this->reportsService->getWeeklyReports();
        $this->assertEquals('1,000', $weeklyReports['totalWithdrawals']);
    }

    public function test_that_weekly_reports_correct_total_funding_amount()
    {
        WalletTransaction::query()->update([
            'transaction_type' => 'CREDIT',
            'description' => 'Fund Wallet',
            'created_at' => now('Africa/Lagos')->subDays(1),
            'amount' => 100
        ]);

        $weeklyReports = $this->reportsService->getWeeklyReports();
        $this->assertEquals('1,000', $weeklyReports['totalFundedAmount']);
    }

    public function test_that_weekly_reports_returns_stakers()
    {
        Staking::query()->update([
            'created_at' => now('Africa/Lagos')->yesterday(),
            'amount_staked' => 400,
            'user_id' => $this->user->id
        ]);

        $weeklyReports = $this->reportsService->getWeeklyReports();
        $this->assertCount(1, $weeklyReports['stakers']);
    }
}
