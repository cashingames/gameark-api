<?php

namespace App\Http\ResponseHelpers;

use App\Models\GameSession;

class GameSessionResponse{

    public function transform(GameSession $gameSession){
        $response = new GameSessionResponse;
        $response->id = $gameSession->id;
        $response->game_mode_id = $gameSession->game_mode_id;
        $response->game_type_id = $gameSession->game_type_id;
        $response->category_id = $gameSession->category_id;
        $response->user_id = $gameSession->user_id;
        $response->start_time = $gameSession->start_time;
        $response->end_time = $gameSession->end_time;
        $response->session_token = $gameSession->session_token;
        $response->correct_count = $gameSession->correct_count;
        $response->user_level = $gameSession->user_level;
        $response->coins_earned = $gameSession->coins_earned;
        $response->wrong_count = $gameSession->wrong_count;
        $response->total_count = $gameSession->total_count;
        $response->points_gained = $gameSession->points_gained;
        $response->state = $gameSession->state;
     
    
        return $response;
    }
}