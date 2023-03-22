<?php

namespace App\Services;

use App\Models\ExhibitionStaking;
use Carbon\Carbon;
use App\Models\GameSession;
use App\Models\Staking;
use App\Models\WalletTransaction;
use App\Traits\Utils\DateUtils;
use Illuminate\Support\Facades\DB;

class AutomatedReportsService
{
    use DateUtils;

    public $netProfit;
    public $totalStakedamount;
    public $totalAmountWon;
    public $totalFundedAmount;
    public $stakers;
    public $completedStakingSessionCount;
    public $totalWithdrawals;
    public $completedStakingSessionsCount;
    public $incompleteStakingSessionsCount;
    public $totalUsedBoostCount;
    public $totalPurchasedBoostCount;
    public $uniqueStakersCount;
    public $totalPurchasedBoostAmount;
    public $timeFreezeboostBoughtCount;
    public $timeFreezeboostBoughtAmount;
    public $skipBoostBoughtAmount;
    public $skipBoostBoughtCount;
    public $averageStakesPerStaker;
    public $averageBoostUsedPerGameSession;
    public $totalGameSessions;
    public $totalStakes;

    public function getDailyReports()
    {
        $startDate = $this->toNigeriaTimeZoneFromUtc(Carbon::yesterday()->startOfDay());
        $endDate = $this->toNigeriaTimeZoneFromUtc(Carbon::yesterday()->endOfDay());


        $this->netProfit = $this->getPlatformProfit($startDate, $endDate);

        $this->totalFundedAmount = WalletTransaction::where('transaction_type', 'CREDIT')
            ->where('description', 'Fund Wallet')->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->sum('amount');
        $this->totalWithdrawals = WalletTransaction::where('transaction_type', 'DEBIT')
            ->where('description', 'Winnings Withdrawal Made')->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)->sum('amount');
      
        $this->totalAmountWon = Staking::where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->sum('amount_won');

        $this->totalStakedamount = Staking::where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->sum('amount_staked');

        $this->completedStakingSessionsCount = $this->getCompletedStakingSessionsCount($startDate, $endDate);

        $this->incompleteStakingSessionsCount = $this->getIncompleteStakingSessionsCount($startDate, $endDate);
        $this->totalUsedBoostCount = $this->getTotalUsedBoostCount($startDate, $endDate);
        $this->totalPurchasedBoostCount = $this->getTotalPurchasedBoostsCount($startDate, $endDate);
        $this->uniqueStakersCount = $this->getTotalNumberOfUniqueStakersInADay($startDate);
        $this->stakers = $this->getTopDailyStakers($startDate);

        $data = [
            'netProfit' => number_format($this->netProfit),
            'totalFundedAmount' => number_format($this->totalFundedAmount),
            'totalWithdrawals' => number_format($this->totalWithdrawals),
            'totalAmountWon' => number_format($this->totalAmountWon),
            'totalStakedAmount' => number_format($this->totalStakedamount),
            'completedStakingSessionsCount' => $this->completedStakingSessionsCount,
            'incompleteStakingSessionsCount' => $this->incompleteStakingSessionsCount,
            'totalUsedBoostCount' => $this->totalUsedBoostCount,
            'totalPurchasedBoostCount' =>  $this->totalPurchasedBoostCount,
            'uniqueStakersCount' => $this->uniqueStakersCount,
            'stakers' => $this->stakers,

        ];

        return $data;
    }

    public function getWeeklyReports()
    {
        $startDate = $this->toNigeriaTimeZoneFromUtc(Carbon::yesterday()->startOfWeek());
        $endDate = $this->toNigeriaTimeZoneFromUtc(Carbon::yesterday()->endOfWeek());

        $this->totalAmountWon = Staking::where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->sum('amount_won');
        $this->totalStakedamount = Staking::where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->sum('amount_staked');
        $this->netProfit = $this->getPlatformProfit($startDate, $endDate);
        $this->stakers = $this->getTopWeeklyStakers($startDate, $endDate);
        $this->totalFundedAmount = WalletTransaction::where('transaction_type', 'CREDIT')
            ->where('description', 'Fund Wallet')->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->sum('amount');
        $this->totalWithdrawals = WalletTransaction::where('transaction_type', 'DEBIT')
            ->where('description', 'Winnings Withdrawal Made')->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)->sum('amount');
        $this->completedStakingSessionsCount = $this->getCompletedStakingSessionsCount($startDate, $endDate);
        $this->incompleteStakingSessionsCount = $this->getIncompleteStakingSessionsCount($startDate, $endDate);
        $this->totalUsedBoostCount = $this->getTotalUsedBoostCount($startDate, $endDate);
        $this->totalPurchasedBoostCount = $this->getTotalPurchasedBoostsCount($startDate, $endDate);
        $this->uniqueStakersCount = $this->getTotalNumberOfUniqueStakersInAWeek($startDate, $endDate);
        $this->timeFreezeboostBoughtAmount = $this->getTimeFreezePurchasedBoostsAmount($startDate, $endDate);
        $this->timeFreezeboostBoughtCount = $this->getTimeFreezePurchasedBoostsCount($startDate, $endDate);
        $this->skipBoostBoughtAmount = $this->getSkipPurchasedBoostsAmount($startDate, $endDate);
        $this->skipBoostBoughtCount = $this->getSkipPurchasedBoostsCount($startDate, $endDate);
        // $this->totalPurchasedBoostAmount = $this->getTotalPurchasedBoostAmount($startDate, $endDate);
        $this->totalGameSessions = $this->getTotalGameSessions($startDate, $endDate);
        $this->totalStakes = $this->getTotalStakes($startDate, $endDate);
        // dd($this->totalGameSessions );
        $this->averageBoostUsedPerGameSession = $this->totalUsedBoostCount / $this->totalGameSessions;
        $this->averageStakesPerStaker = $this->uniqueStakersCount / $this->totalStakes;

        $data = [
            'netProfit' => number_format($this->netProfit),
            'stakers' => $this->stakers,
            'totalFundedAmount' => number_format($this->totalFundedAmount),
            'totalWithdrawals' => number_format($this->totalWithdrawals),
            'completedStakingSessionsCount' => $this->completedStakingSessionsCount,
            'totalAmountWon' => number_format($this->totalAmountWon),
            'totalStakedamount' => number_format($this->totalStakedamount),
            'incompleteStakingSessionsCount' => $this->incompleteStakingSessionsCount,
            'totalUsedBoostCount' => $this->totalUsedBoostCount,
            'totalPurchasedBoostCount' =>  $this->totalPurchasedBoostCount,
            'uniqueStakersCount' => $this->uniqueStakersCount,
            // 'totalPurchasedBoostAmount' => number_format($this->totalPurchasedBoostAmount),
            'timeFreezeboostBoughtAmount' => number_format($this->timeFreezeboostBoughtAmount),
            'timeFreezeboostBoughtCount' => $this->timeFreezeboostBoughtCount,
            'skipBoostBoughtAmount' => number_format($this->skipBoostBoughtAmount),
            'skipBoostBoughtCount' => $this->skipBoostBoughtCount,
            'averageStakesPerStaker' => round($this->averageStakesPerStaker, 3),
            'averageBoostUsedPerGameSession' => round($this->averageBoostUsedPerGameSession, 3),
        ];

        return $data;
    }

    private function getPlatformProfit($startDate, $endDate)
    {
        $stakes = Staking::where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate);

        $amountStaked = $stakes->sum('stakings.amount_staked') ?? 0;
        $amountWon = $stakes->sum('stakings.amount_won') ?? 0;

        if ($amountStaked == 0) {
            return 0;
        }

        return $amountStaked - $amountWon;
    }

    private function getTopDailyStakers(Carbon $startDate)
    {
        $stakers = DB::select("select
            sum(amount_staked) staked, sum(amount_won) won,
            (sum(amount_staked) - sum(amount_won)) profit, ((sum(amount_staked)/sum(amount_won))-1)*100 profit_perc,
            users.username, users.email
            from stakings
            left join users on users.id = stakings.user_id
            where date(stakings.created_at) = '{$startDate->toDateString()}'
            group by user_id
            having sum(amount_staked) > 400
            order by staked desc");

        return $stakers;
    }

    private function getTopWeeklyStakers(Carbon $startDate, Carbon $endDate)
    {
        $stakers = DB::select("select
            sum(amount_staked) staked, sum(amount_won) won,
            (sum(amount_staked) - sum(amount_won)) profit, ((sum(amount_staked)/sum(amount_won))-1)*100 profit_perc,
            users.username, users.email
            from stakings
            left join users on users.id = stakings.user_id
            where date(stakings.created_at) BETWEEN '{$startDate->toDateString()}'
            and '{$endDate->toDateString()}'
            group by user_id
            having sum(amount_staked) > 400
            order by staked desc");

        return $stakers;
    }

    private function getCompletedStakingSessionsCount($startDate, $endDate)
    {
        return GameSession::whereHas('exhibitionStaking')->where('state', '=', 'COMPLETED')
            ->whereBetween('created_at', [$startDate->toDateString(), $endDate->toDateString()])->count();
    }

    private function getIncompleteStakingSessionsCount($startDate, $endDate)
    {
        return GameSession::whereHas('exhibitionStaking')->where('state', '=', 'ONGOING')
            ->whereBetween('created_at', [$startDate->toDateString(), $endDate->toDateString()])->count();
    }

    private function getTotalUsedBoostCount(Carbon $startDate, Carbon $endDate)
    {
        return DB::table('exhibition_boosts')->whereBetween('created_at', [$startDate->toDateString(), $endDate->toDateString()])->count();
    }

    private function getTotalPurchasedBoostsCount($startDate, $endDate){
       return WalletTransaction::where('description','LIKE','Bought TIME FEEZE boosts')
        ->orWhere('description','LIKE','Bought SKIP boosts')->where('created_at', '>=', $startDate)
        ->where('created_at', '<=', $endDate)->count();
    }

    private function getTotalNumberOfUniqueStakersInADay(Carbon $date)
    {
        return Staking::whereDate('created_at', '=', $date->toDateString())->distinct('user_id')->count();;
    }

    private function getTotalNumberOfUniqueStakersInAWeek(Carbon $startDate, Carbon $endDate)
    {
        return Staking::whereBetween('created_at',  [$startDate->toDateString(), $endDate->toDateString()])
            ->distinct('user_id')->count();
    }

    // private function getTotalPurchasedBoostAmount($startDate, $endDate)
    // {
    //     return WalletTransaction::where('description', 'LIKE', 'Bought TIME FEEZE boosts')
    //         ->orWhere('description', 'LIKE', 'bought SKIP boosts')->where('created_at', '>=', $startDate)
    //         ->where('created_at', '<=', $endDate)->sum('amount');
    // }

    private function getTimeFreezePurchasedBoostsCount($startDate, $endDate)
    {
        return WalletTransaction::where('description', 'LIKE', 'Bought TIME FEEZE boosts')->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)->count();
    }

    private function getTimeFreezePurchasedBoostsAmount($startDate, $endDate)
    {
        return WalletTransaction::where('description', 'LIKE', 'Bought TIME FEEZE boosts')->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)->sum('amount');
    }
    private function getSkipPurchasedBoostsAmount($startDate, $endDate)
    {
        return WalletTransaction::where('description', 'LIKE', 'Bought SKIP boosts')->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)->sum('amount');
    }
    private function getSkipPurchasedBoostsCount($startDate, $endDate)
    {
        return WalletTransaction::where('description', 'LIKE', 'Bought SKIP boosts')->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)->count();
    }

    private function getTotalStakes($startDate, $endDate)
    {
        $value = Staking::where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)->count();
        return $value == 0 ? 1 : $value;

    }

    private function getTotalGameSessions($startDate, $endDate)
    {
        $value = GameSession::where('created_at', '>=', $startDate)->where('created_at', '<=', $endDate)->count();
        return $value == 0 ? 1 : $value;
    }
}
