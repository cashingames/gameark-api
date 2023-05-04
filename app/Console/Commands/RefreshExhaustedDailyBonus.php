<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Models\UserPlan;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Support\Facades\DB;
use App\Models\FcmPushSubscription;
use App\Actions\SendPushNotification;
use App\Enums\ClientPlatform;

class RefreshExhaustedDailyBonus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bonus:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Exhausted Free Game Plan ';

    public $incrementCount = 5;
    public $incrementCountLimit = 15;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $pushNotification = new SendPushNotification(ClientPlatform::GameArkMobile);
        // $today = Carbon::now()->endOfDay();
        $freePlan = Plan::where('is_free', true)->first();

        $pushTokens = $this->renewExhaustedBonusToIncrementLimit($freePlan);

        $pushNotification->sendDailyReminderNotification(
            $pushTokens,
            true,
            "GameArk",
            "Good news! Your GameArk lives have been refreshed and you're ready to continue your adventure. Jump back into the game and conquer those challenging levels with your new set of lives. 👍🎮");
    }

    public function renewExhaustedBonusToIncrementLimit($freePlan){
        $pushTokens = [];
        User::all()->map(function ($user) use ($pushTokens, $freePlan) {

            if(!($user->hasActiveFreePlan())){
                UserPlan::create([
                    'plan_id' => $freePlan->id,
                    'user_id' => $user->id,
                    'description' => "Refreshing daily plan for " . $user->username,
                    'used_count' => 0,
                    'plan_count' => 5,
                    'is_active' => true,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'expire_at' => Carbon::now()->endOfDay()
                ]);

                $device_token = FcmPushSubscription::where('user_id', $user->id)->latest()->first();
                if (!is_null($device_token)) {
                    $pushTokens[] = $device_token->device_token;
                }
            }
        });

        return $pushTokens;
    }
}
