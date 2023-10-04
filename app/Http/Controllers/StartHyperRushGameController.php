<?php

namespace App\Http\Controllers;

use App\Http\ResponseHelpers\ResponseHelper;
use App\Models\Category;
use App\Models\Question;
use App\Services\HyperRushGameService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use stdClass;

class StartHyperRushGameController extends BaseController
{
    public function __invoke(
        Request $request,
        HyperRushGameService $gameService
    )
    {      
        $request->validate([
            'category' => ['required', 'string'],
        ]);

        $hyperRushCategory = Category::where('name', $request->category)->first();

        if(is_null($hyperRushCategory) || $hyperRushCategory->name != $request->category)
        {
            Log::info('HYPER_RUSH_GAME_CANNOT_START_DUE_TO_WRONG_CATEGORY', [
                'user' => $request->user()->username,
            ]);
            return ResponseHelper::error('Category not available for now, try again later', 400);
        }

        $startResponse = $gameService->getHyperRushQuestions($hyperRushCategory->id);

        //@TODO: Handle business error states in the services
        if (count($startResponse['questions']) < 10) {
            Log::info('HYPER_RUSH_GAME_CANNOT_START', [
                'user' => $request->user()->username,
            ]);
            return ResponseHelper::error('Category not available for now, try again later', 400);
        }

        $result = $this->prepare($startResponse['gameSession'], $startResponse['questions']);
        return $this->sendResponse($result, 'Success');

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

