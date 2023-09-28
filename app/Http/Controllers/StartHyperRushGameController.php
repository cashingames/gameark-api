<?php

namespace App\Http\Controllers;

use App\Http\ResponseHelpers\ResponseHelper;
use App\Models\Question;
use App\Services\HyperRushGameService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use stdClass;

class StartHyperRushGameController extends Controller
{
    public function __invoke(
        Request $request,
        HyperRushGameService $gameService
    )
    {
        $startResponse = $gameService->getHyperRushQuestions();

        //@TODO: Handle business error states in the services
        if (count($startResponse['questions']) < 10) {
            Log::info('SSTART_SINGLE_PLAYER_CANNOT_START', [
                'user' => $request->user()->username,
            ]);
            return ResponseHelper::error('Category not available for now, try again later', 400);
        }

        $result = $this->prepare($startResponse['gameSession'], $startResponse['questions']);
        return ResponseHelper::success($result);

        }

        private function prepare($gameSession, $questions): array
    {
        $gameInfo = new stdClass;
        $gameInfo->token = $gameSession->session_token;
        $gameInfo->startTime = $gameSession->start_time;
        $gameInfo->endTime = $gameSession->end_time;

        return [
            'questions' => $questions,
            'game' => $gameInfo
        ];
    }
    }

