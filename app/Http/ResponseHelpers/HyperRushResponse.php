<?php

namespace App\Http\ResponseHelpers;

use App\Models\HyperRushGameSession;

class HyperRushResponse{

    public function transform(HyperRushGameSession $gameSession){
        $response = new HyperRushResponse;
        $response->id = $gameSession->id;
        $response->user_id = $gameSession->user_id;
        $response->start_time = $gameSession->start_time;
        $response->end_time = $gameSession->end_time;
        $response->session_token = $gameSession->session_token;
        $response->correct_count = $gameSession->correct_count;
        $response->wrong_count = $gameSession->wrong_count;
        $response->total_count = $gameSession->total_count;
        $response->points_gained = $gameSession->points_gained;
        $response->state = $gameSession->state;
        $response->score = $gameSession->score;
        $response->high_score = $gameSession->high_score;
    
        return $response;
    }
}