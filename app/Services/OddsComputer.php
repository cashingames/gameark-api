<?php

namespace App\Services;

use App\Models\User;
use App\Traits\Utils\DateUtils;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OddsComputer{

    use DateUtils;

    public function compute(User $user, $averageScoreOfRecentGames): array{
        $averageScoreOfRecentGames = is_numeric($averageScoreOfRecentGames) ? floor($averageScoreOfRecentGames) : $averageScoreOfRecentGames;

        $oddsMultiplier = 1;
        $oddsCondition = "no_matching_condition";
        
        if($this->isNewPlayer($user)){

            $newUserRulesAndConditions = DB::table('odds_conditions_and_rules')->where('rule', 'GAME_COUNT_LESS_THAN_5')->first();    
            $oddsMultiplier = $newUserRulesAndConditions->odds_benefit;
            $oddsCondition = $newUserRulesAndConditions->condition;
        }
        elseif ($averageScoreOfRecentGames <= 4){

            $lowScoreRulesAndConditions = DB::table('odds_conditions_and_rules')->where('rule', 'AVERAGE_SCORE_LESS_THAN_5')->first();    
            $oddsMultiplier = $lowScoreRulesAndConditions->odds_benefit;
            $oddsCondition = $lowScoreRulesAndConditions->condition;
        }
        elseif ($averageScoreOfRecentGames >= 5 && $averageScoreOfRecentGames <= 7){

            $moderateScoreRulesAndConditions = DB::table('odds_conditions_and_rules')->where('rule', 'AVERAGE_SCORE_BETWEEN_5_AND_7')->first();    
            $oddsMultiplier = $moderateScoreRulesAndConditions->odds_benefit;
            $oddsCondition = $moderateScoreRulesAndConditions->condition;
        }
        elseif($averageScoreOfRecentGames > 7){

            $highScoreRulesAndConditions = DB::table('odds_conditions_and_rules')->where('rule', 'AVERAGE_SCORE_GREATER_THAN_7')->first();    
            $oddsMultiplier = $highScoreRulesAndConditions->odds_benefit;
            $oddsCondition = $highScoreRulesAndConditions->condition;
        }

        if ($this->currentTimeIsInSpecialHours() && $averageScoreOfRecentGames <= 4) {

            $specialHourRulesAndConditions = DB::table('odds_conditions_and_rules')->where('rule', 'AT_SPECIAL_HOUR')->first();    
            $oddsMultiplier += $specialHourRulesAndConditions->odds_benefit;
            $oddsCondition .= "_and_".$specialHourRulesAndConditions->condition;   
        }
        if ($this->isFirstGameAfterFunding($user)){

            $fundingWalletRulesAndConditions = DB::table('odds_conditions_and_rules')->where('rule', 'FIRST_TIME_GAME_AFTER_FUNDING')->first();    
            $oddsMultiplier += $fundingWalletRulesAndConditions->odds_benefit;
            $oddsCondition .= "_and_".$fundingWalletRulesAndConditions->condition;
        }
        return [
            'oddsMultiplier' => $oddsMultiplier,
            'oddsCondition' => $oddsCondition
        ];
    }

    public function currentTimeIsInSpecialHours(){
        $now = date("H");
        $now = $this->toNigeriaTimeZoneFromUtc(date("Y-m-d H:i:s"))->format("H");
        $now .= ":00";
        
        $specialHours = config('odds.special_hours');
        
        return in_array($now, $specialHours);
    }

    public function isFirstGameAfterFunding(User $user){
        $last_funding = $user->transactions()->where('transaction_type', 'CREDIT')->where('description', 'like', "fund%")->latest()->first();
        $last_game = $user->gameSessions()->latest()->first();
        if (is_null($last_funding) || is_null($last_game)){
            return false;
        }

        return Carbon::createFromDate($last_funding->created_at)->gt(Carbon::createFromDate($last_game->created_at));
    }

    public function isNewPlayer(User $user){
        return $user->gameSessions()->count() <= 5 ;
    }
}