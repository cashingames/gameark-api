<?php

namespace App\Services;

use App\Models\HyperRushGameSession;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HyperRushGameService
{

    public function getHyperRushQuestions()
    {
        $questions = Question::where('trivia_type', 'hyper_rush')->get();

        if ($questions) {

            DB::beginTransaction();
            $gameSession = $this->generateSession();
            $this->logQuestions($questions, $gameSession);
            DB::commit();
            return [
                'gameSession' => $gameSession,
                'questions' => $questions,
            ];
        }
    }

    private function generateSession(): HyperRushGameSession
    {
        $gameSession = new HyperRushGameSession();
        $gameSession->user_id = auth()->user()->id;
        $gameSession->session_token = Str::random(40);
        $gameSession->start_time = Carbon::now();
        $gameSession->end_time = Carbon::now()->addMinutes(1);
        $gameSession->state = "ONGOING";
        $gameSession->save();
        return $gameSession;
    }

    private function logQuestions($questions, $gameSession): void
    {
        $data = [];

        foreach ($questions as $question) {
            $data[] = [
                'question_id' => $question->id,
                'hyper_rush_game_session_id' => $gameSession->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('hyper_rush_session_questions')->insert($data);
    }
}
