<?php

namespace App\Services\SMS;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use App\Services\SMS\SMSProviderInterface;

class TermiiService implements SMSProviderInterface{

    protected $baseUrl = "https://api.ng.termii.com";

    protected $apiKey;


    protected $networkClient;

    public function __construct(string $apiKey){
        $this->apiKey = $apiKey;
        $this->networkClient = new Client(
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'base_uri' => $this->baseUrl
            ]
        );
    }

    public function setApiKey($apiKey){
        $this->apiKey = $apiKey;
        return $this;
    }

    public function getApiKey(){
        return $this->apiKey;
    }

    /**
     * Send message to recipient
     * 
     * @param $data array
     */
    public function send(array $data){
        $data['api_key'] = $this->getApiKey();
        $data['to'] = $this->internationalizePhoneNumber($data['to']);
        !isset($data['channel']) ? $data['channel'] = "dnd" : $data['channel'];
        !isset($data['type']) ? $data['type'] = "plain": $data['type'];

        $response = $this->networkClient->request("POST", "/api/sms/send", ['json' => $data, 'verify' => false]);
        return json_decode($response->getBody());
    }

    public function deliverOTP($user){
        $smsData = [
            'to' => $user->phone_number,
            'channel' => 'dnd',
            'type' => 'plain',
            'from' => "N-Alert",
            'sms' => "{$user->username}, your Cashingames secure OTP is {$user->otp_token}. Do not share with anyone"
        ];
        try {
            $this->send($smsData);
            Cache::put($user->username . "_last_otp_time", now()->toTimeString(), $seconds = 120);
            
        } catch (\Throwable $th) {

            throw $th;
            // return $this->sendResponse("Unable to deliver OTP via SMS", "Reason: " . $th->getMessage());
        }
    }

    private function internationalizePhoneNumber($phoneNumber, $country="ng"){
        $countries = [
            'ng' => '234'
        ];
        if (!in_array($country, $countries)){
            $country = "ng";
        }
        $lastTenDigits = substr($phoneNumber, -10);
        return $countries[$country] . "" . $lastTenDigits;
    }
}