<?php

namespace App\Http\Controllers;

use App\Models\Boost;
use App\Models\Reward;
use App\Models\RewardBenefit;
use App\Models\User;
use App\Models\UserReward;
use Illuminate\Http\Request;

class ClaimUserRewardController extends BaseController
{
    public function __invoke(Request $request)
    {

        $request->validate([
            'day' => ['required', 'integer'],
        ]);

        $user = auth()->user();

        $userLastRecord = UserReward::where('user_id', $user->id)->where('reward_milestone', $request->day)->first();

        if ($userLastRecord->reward_count > 0) {
            return $this->sendResponse('Reward Claimed', 'Reward Claimed');
        }

        if ($userLastRecord) {
            $userLastRecord->reward_count = 1;
            $userLastRecord->save();
        }

        $userRewardRecordCount = $userLastRecord->reward_milestone;

        $rewardClaimableDays = RewardBenefit::where('reward_benefit_id', $userRewardRecordCount)->get();
        foreach ($rewardClaimableDays as $rewardEachDay) {
            if ($rewardEachDay->reward_type == 'boost' && $userRewardRecordCount > 0) {
                $boostId = Boost::where('name', $rewardEachDay->reward_name)->first()->id;
                $userBoost = $user->boosts()->where('boost_id', $boostId)->first();

                if ($userBoost === null) {
                    $user->boosts()->create([
                        'boost_id' => Boost::where('name', $rewardEachDay->reward_name)->first()->id,
                        'boost_count' => $rewardEachDay->reward_count,
                        'used_count' => 0,
                    ]);
                } else {
                    $userBoost->update(['boost_count' => $userBoost->boost_count + $rewardEachDay->reward_count]);
                }
            }

            if ($rewardEachDay->reward_type == 'coins') {
                $userCoin = $user->userCoins()->firstOrNew();
                $userCoin->coins_value = $userCoin->coins_value + $rewardEachDay->reward_count;
                $userCoin->save();

                $user->coinsTransaction()->create([
                    'transaction_type' => 'CREDIT',
                    'description' => 'Daily reward coins awarded',
                    'value' => $rewardEachDay->reward_count,
                ]);
            }
        }

        if ($request->day < 7) {
            $reward = Reward::where('name', 'daily_rewards')->first();

            if ($reward) {
                UserReward::create([
                    'user_id' => $user->id,
                    'reward_id' => $reward->id,
                    'reward_count' => 0,
                    'reward_date' => now(),
                    'release_on' => now(),
                    'reward_milestone' => $userRewardRecordCount + 1,
                ]);
            }
        }

        return $this->sendResponse('Reward Claimed', 'Reward Claimed');
    }
}
