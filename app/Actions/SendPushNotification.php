<?php

namespace App\Actions;

use App\Enums\ClientPlatform;
use App\Enums\PushNotificationType;
use App\Models\FcmPushSubscription;
use App\Models\LiveTrivia;
use App\Services\Firebase\CloudMessagingService;
use App\Traits\Utils\DateUtils;
use Illuminate\Support\Facades\Log;

class SendPushNotification
{

    use DateUtils;
    /**
     * @var App\Services\Firebase\CloudMessaging
     */
    public $pushService;

    public function __construct(ClientPlatform $clientPlatform = ClientPlatform::CashingamesMobile)
    {
        $fcm;
        switch ($clientPlatform) {
            case ClientPlatform::GameArkMobile :
                $fcm = config('services.firebase.gameark_server_key');
                break;

            case ClientPlatform::CashingamesMobile :
            default:
                $fcm = config('services.firebase.server_key');
                break;
        }

        $this->pushService = new CloudMessagingService($fcm);
    }

    public function sendChallengeInviteNotification($sender, $opponent, $challenge)
    {
        $recipient = FcmPushSubscription::where('user_id', $opponent->id)->latest()->first();
        if (is_null($recipient)) {
            return;
        }
        $this->pushService->setNotification(
            [
                'title' => "Cashingames Invitation! : Play a Challenge Game!",
                'body' => "Your friend, {$sender->username} has just sent you a challenge invite"
            ]
        )
            ->setData(
                [

                    'title' => "Cashingames Invitation! : Play a Challenge Game!",
                    'body' => "Your friend, {$sender->username} has just sent you a challenge invite",
                    'action_type' => PushNotificationType::Challenge,
                    'action_id' => $challenge->id,
                    'unread_notifications_count' => $opponent->unreadNotifications()->count()

                ]
            )
            ->setTo($recipient->device_token)
            ->send();
        Log::info("Challenge invitation push notification sent to: " . $opponent->username . " from " . $sender->username);
    }

    public function sendChallengeStatusChangeNotification($player, $opponent, $challenge, $status)
    {
        $recipient = FcmPushSubscription::where('user_id', $player->id)->latest()->first();
        if (is_null($recipient)) {
            return;
        }

        $this->pushService->setNotification(
            [
                'title' => "Cashingames Challenge Status Update",
                'body' => "Your opponent, {$opponent->username} has {$status} your invite"
            ]
        )
            ->setData(
                [
                    'title' => "Challenge Status Update",
                    'body' => "Your opponent, {$opponent->username} has {$status} your invite",
                    'action_type' => PushNotificationType::Challenge,
                    'action_id' => $challenge->id,
                    'unread_notifications_count' => $player->unreadNotifications()->count()
                ]
            )
            ->setTo($recipient->device_token)
            ->send();
        Log::info("Challenge status update push notification sent to: " . $player->username . " from " . $opponent->username);
    }
    public function sendChallengeCompletedNotification($user, $challenge)
    {

        if ($user->id == $challenge->user_id) {
            $recipient = $challenge->opponent;
        } else {
            $recipient = $challenge->users;
        }
        $device_token = FcmPushSubscription::where('user_id', $recipient->id)->latest()->first();
        if (is_null($device_token)) {
            return;
        }
        $this->pushService->setNotification(
            [
                'title' => "Cashingames Challenge Completed!",
                'body' => "Your opponent, {$user->username} has completed the challenge, check the scores now"
            ]
        )
            ->setData(
                [

                    'title' => "Challenge Completed!",
                    'body' => "Your opponent, {$user->username} has completed the challenge, check the scores now",
                    'action_type' => PushNotificationType::Challenge,
                    'action_id' => $challenge->id,
                    'unread_notifications_count' => $recipient->unreadNotifications()->count()

                ]
            )
            ->setTo($device_token->device_token)
            ->send();

        Log::info("Challenge invitation push notification sent to: " . $recipient->username . " from " . $user->username);
    }

    public function sendSpecialHourOddsNotification($user, $hasMany = false)
    {

        $instance = $this->pushService->setNotification(
            [
                'title' => "Special Hour: Play now and win more",
                'body' => "Play a game now and increase your odds of winning by x1.5"
            ]
        )
            ->setData(
                [

                    'title' => "Special Hour: Play now and win more",
                    'body' => "Play a game now and increase your odds of winning by x1.5",
                    'action_type' => "#",
                    'action_id' => "#"

                ]
            );

        if($hasMany){
            $instance->setTo($user)
            ->sendToMany();
        }else{
            $device_token = FcmPushSubscription::where('user_id', $user->id)->latest()->first();
            if (is_null($device_token)) {
                return;
            }

            $instance->setTo($device_token->device_token)
            ->send();

        }
    }

    public function sendliveTriviaNotification($device, $time, $hasMany = false)
    {
        $instance = $this->pushService->setNotification(
            [
                'title' => "Live Trivia Alert ! : Play $time !",
                'body' => "Play this live trivia and stand a chance to win cash!"
            ]
        )
            ->setData(
                [

                    'title' => "Live Trivia Alert ! : Play $time !",
                    'body' => "Play this live trivia and stand a chance to win cash!",
                    'action_type' => "#",
                    'action_id' => "#"

                ]
            );

        if($hasMany){
            $instance->setTo($device)
            ->sendToMany();
        }else{
            $instance->setTo($device->device_token)
            ->send();
        }
    }

    public function sendChallengeStakingRefundNotification($player, $challenge)
    {
        $recipient = FcmPushSubscription::where('user_id', $player->id)->latest()->first();
        if (is_null($recipient)) {
            return;
        }

        $this->pushService->setNotification(
            [
                'title' => "Cashingames Challenge Staking Refund",
                'body' => "Your challenge staking has been refunded"
            ]
        )
            ->setData(
                [
                    'title' => "Challenge Staking Refund",
                    'body' => "Your challenge staking has been refunded",
                    'action_type' => PushNotificationType::Challenge,
                    'action_id' => $challenge->id,
                    'unread_notifications_count' => $player->unreadNotifications()->count()
                ]
            )
            ->setTo($recipient->device_token)
            ->send();
        Log::info("Challenge staking refund push notification sent to: " . $player->username );
    }

    public function sendDailyBonusGamesNotification($device, $hasMany = false)
    {
        $instance = $this->pushService->setNotification(
            [
                'title' => "You have been awarded free games!",
                'body' => "Play now and stand a chance to win cash!"
            ]
        )
            ->setData(
                [

                    'title' => "You have been awarded free games!",
                    'body' => "Play now and stand a chance to win cash!",
                    'action_type' => "#",
                    'action_id' => "#"

                ]
            );

            if($hasMany){
                $instance->setTo($device)->sendToMany();
            }else{
                $instance->setTo($device->device_token)->send();
            }
    }

    public function sendBoostsReminderNotification($user, $hasMany)
    {
        $instance = $this->pushService->setNotification(
            [
                'title' => "Your boosts are your super powers!",
                'body' => "Use boosts to stand a better chance at winning!"
            ]
        )
            ->setData(
                [

                    'title' => "Your boosts are your super powers!",
                    'body' => "Use boosts to stand a better chance at winning!",
                    'action_type' => "#",
                    'action_id' => "#"

                ]
            );

        if($hasMany){
            $instance->setTo($user)->sendToMany();
        }else{
            $device_token = FcmPushSubscription::where('user_id', $user->id)->latest()->first();
            if (is_null($device_token)) {
                return;
            }

            $instance->setTo($device_token->device_token)->send();
        }

    }

    public function sendInAppActivityNotification($user, $message)
    {
        $device_token = FcmPushSubscription::where('user_id', $user->id)->latest()->first();
        if (is_null($device_token)) {
            return;
        }

        $this->pushService->setNotification(
            [
                'title' => "Hi there!",
                'body' => $message
            ]
        )
            ->setData(
                [

                    'title' => "Hi there!",
                    'body' => $message,
                    'action_id' => '#',
                    'unread_notifications_count' => $user->unreadNotifications()->count()

                ]
            )
            ->setTo($device_token->device_token)
            ->send();
    }

    public function sendReferralBonusNotification($user, $plan_count)
    {
        $device_token = FcmPushSubscription::where('user_id', $user->user_id)->latest()->first();
        if (is_null($device_token)) {
            Log::info('Logger: no device token for');
            Log::info($user);
            return;
        }

        $this->pushService->setNotification(
            [
                'title' => "You've Been Rewarded For Referral!",
                'body' => "Bonus Plan of 2 Games has been rewarded for your referralg!"
            ]
        )
            ->setData(
                [

                    'title' => "You've Been Rewarded For Referral!",
                    'body' => "Bonus Plan of $plan_count Games has been rewarded for your referral!",
                    'action_type' => "#",
                    'action_id' => "#"

                ]
            )
            ->setTo($device_token->device_token)
            ->send();

        Log::info('Logger: FCM Sent TO-> '.$device_token->device_token);
    }

    public function sendDailyReminderNotification($usersToken, $hasMany = false, $title, $msg)
    {
        if(is_array($usersToken)){
            // divide the tokens into 900
            $maxPerBatch = 900;
            $totalToken = count($usersToken);
            $length = ($totalToken > $maxPerBatch) ? ceil($totalToken / $maxPerBatch) : 1 ;
            $current = 0;

            for ($i=0; $i < $length ; $i++) {

                $tokens = [];
                // fetch upto 900 from list
                for($j = 0 ; $j < $maxPerBatch; $j++){

                    if($current < $totalToken){
                        $tokens[] = $usersToken[$current];
                    }
                    $current++;
                }

                $this->sendNotificationInBatches($tokens, $hasMany, $title, $msg);
            }
        }else{
            $this->sendNotificationInBatches($usersToken, $hasMany, $title, $msg);
        }

    }

    public function sendNotificationInBatches($user, $hasMany = false, $title, $msg)
    {
        $instance = $this->pushService->setNotification(
            [
                'title' => $title,
                'body' => $msg
            ]
        )
            ->setData(
                [

                    'title' => $title,
                    'body' => $msg,
                    'action_type' => "#",
                    'action_id' => "#"

                ]
            );

        if($hasMany){
            $instance->setTo($user)
            ->sendToMany();
        }else{
            $device_token = FcmPushSubscription::where('user_id', $user->id)->latest()->first();
            if (is_null($device_token)) {
                return;
            }

            $instance->setTo($device_token->device_token)
            ->send();

        }
    }

}
