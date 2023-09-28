<?php

namespace App\Http\Controllers;

use App\Http\ResponseHelpers\HyperRushResponse;
use App\Models\HyperRushSessionQuestion;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EndHyperRushGameController extends BaseController
{

    public function __invoke(Request $request)
    {        
        Log::info($request->all());

        $game = $this->user->hyperRushGameSessions()->where('session_token', $request->token)->sharedLock()->first();
        if ($game == null) {
            Log::info($this->user->username . " tries to end game with invalid token " . $request->token);
            return $this->sendError('Game Session does not exist', 'Game Session does not exist');
        }

        if ($game->state == "COMPLETED") {
            Log::info($this->user->username . " trying to end game a second time with " . $request->token);
            return $this->sendResponse($game, 'Game Ended');
        }

        $game->end_time = Carbon::now()->subSeconds(3); //this might be causing negative if the user submitted early
        $game->state = "COMPLETED";

        $points = 0;
        $wrongs = 0;
        $score = 0;

        $chosenOptions = [];

        DB::transaction(function () use ($chosenOptions, $game) {
            foreach ($chosenOptions as $value) {
                HyperRushSessionQuestion::where('hyper_rush_game_session_id', $game->id)
                    ->where('question_id', $value['question_id'])
                    ->update(['option_id' => $value['id']]);
            }
        });

        $questions = collect(Question::with('options')->whereIn('id', array_column($chosenOptions, 'question_id'))->get());

        foreach ($chosenOptions as $a) {
            $isCorect = $questions->firstWhere('id', $a['question_id'])->options->where('id', $a['id'])->where('is_correct', true)->first();

            if ($isCorect != null) {
                $points = $points + 1;
                $score = $score + 10;
            } else {
                $wrongs = $wrongs + 1;
            }
        }

        $game->wrong_count = $wrongs;
        $game->correct_count = $points;
        $game->high_score = $this->user->hyperRushHighScore();
        $game->score = $score;
        $game->total_count = $points + $wrongs;

        $game->save();

        return $this->sendResponse((new HyperRushResponse())->transform($game), "Game Ended");
    }
}
