<?php

namespace App\Services;

use App\Http\ResponseHelpers\DailyRewardResponse;
use App\Jobs\ReactivateUserReward;
use App\Models\Reward;
use App\Models\RewardBenefit;
use App\Models\User;
use Carbon\Carbon;

class DailyRewardService
{
    public function shouldShowDailyReward(User $user)
    {
        $userRewardRecordCount = $user->rewards()->count();
        $reward = Reward::find(1);
        $userLastRecord = $user->rewards()->latest('pivot_created_at')->withPivot('reward_count', 'reward_date', 'reward_milestone', 'release_on')->first();
       
        if ($userRewardRecordCount == 0) {
            $newUserRewardRecord = $user->rewards()->attach($reward->id, [
                'reward_count' => 0,
                'reward_date' => now(),
                'release_on' => now(),
                'reward_milestone' => 1,
            ]);
            $rewardClaimableDay = $this->getTodayReward();
            return response()->json([
                "shouldShowPopup" => true,
                'reward' => $rewardClaimableDay], 200);
        }

        if ($userRewardRecordCount > 0 && $userRewardRecordCount <= 7) {
            $userLastRewardClaimDate = Carbon::parse($userLastRecord->pivot->reward_date);
            $currentDate = Carbon::now();
            if ($userLastRewardClaimDate->isSameDay($currentDate)) {
                return response()->json([
                    "shouldShowPopup" => false,
                    'reward' => []], 200);
            }
            $userRewardCount = $userLastRecord->pivot->reward_count;
            if ($userRewardCount == 0 && !$userLastRewardClaimDate->isSameDay($currentDate)) {
                $rewardClaimableDay = $this->getTodayReward();
                return response()->json([
                    "shouldShowPopup" => true,
                    'reward' => $rewardClaimableDay], 200);
            }
            if ($userRewardCount == -1) {
                dispatch(new ReactivateUserReward());
                return response()->json([
                    "shouldShowPopup" => false,
                    'reward' => []], 200);
            }
        } else {
            return false;
        }
    }

    private function getTodayReward()
    {
        $rewardClaimableDays = RewardBenefit::get();
        $data = [];
        $response = new DailyRewardResponse();
        foreach ($rewardClaimableDays as $rewardEachDay) {
            $data[] = $response->transform($rewardEachDay);
        }
        return $data;
    }
}
 