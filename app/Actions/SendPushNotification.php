<?php

namespace App\Actions;

use App\Enums\PushNotificationType;
use App\Models\FcmPushSubscription;
use App\Services\Firebase\CloudMessagingService;

class SendPushNotification{

    /**
     * @var App\Services\Firebase\CloudMessaging
     */
    public $pushService;

    public function __construct(){
        $this->pushService = new CloudMessagingService(config('services.firebase.server_key'));
    }

    public function sendChallengeInviteNotification($sender, $opponent, $challenge){
        $recipient = FcmPushSubscription::where('user_id', $opponent->id)->latest()->first();
        if (is_null($recipient)){
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
                'action_id' => $challenge->id
            
            ]
        )
        ->setTo($recipient->device_token)
        ->send();
    }

    public function sendChallengeStatusChangeNotification($player, $opponent, $challenge, $status){
        $recipient = FcmPushSubscription::where('user_id', $player->id)->latest()->first();
        if (is_null($recipient)){
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
                'action_id' => $challenge->id
            ]
        )
        ->setTo($recipient->device_token)
        ->send();
    }
    public function sendChallengeCompletedNotification($player, $challenge){
        //if the current player is the creator, the opponent is the recipient
        //else the player is the recipient
        if ($player->id == $challenge->user_id){
            $recipient = $challenge->opponent;
            $opponent = $player;
            
        }else{
            $recipient = $player;
            $opponent = $challenge->opponent;
            
        }
        $device_token = FcmPushSubscription::where('user_id', $recipient->id)->latest()->first();
        if (is_null($recipient)) {
            return;
        }
        $this->pushService->setNotification(
            [
                'title' => "Cashingames Challenge Completed!",
                'body' => "Your opponent, {$opponent->username} has completed the challenge, check the scores now"
            ]
        )
            ->setData(
                [
                    
                    'title' => "Challenge Completed!",
                    'body' => "Your opponent, {$opponent->username} has completed the challenge, check the scores now",
                    'action_type' => PushNotificationType::Challenge,
                    'action_id' => $challenge->id
                
                ]
            )
            ->setTo($device_token->device_token)
            ->send();
    }
}