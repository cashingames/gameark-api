<?php

namespace App\Http\ResponseHelpers;

use App\Enums\LiveTriviaPlayerStatus;
use App\Enums\LiveTriviaStatus;
use App\Models\LiveTrivia;
use App\Models\UserPoint;
use App\Traits\Utils\DateUtils;
use Illuminate\Support\Carbon;
use \Illuminate\Http\JsonResponse;

class LiveTriviaStatusResponse
{
    use DateUtils;

    public int $id;
    public int $typeId;
    public int $modeId;
    public int $categoryId;
    public string $title;
    public string $prize;
    public int $duration;
    public int $questionsCount;
    public int $pointsRequired;
    public string $startAt;
    public int $startAtUtc;
    public LiveTriviaStatus $status; //if its active, closed, or expired
    public LiveTriviaPlayerStatus $playerStatus; //if the user can play, or has has played

    public string $prizeDisplayText;
    public string $statusDisplayText; //we want to control how the status is displayed to the user
    public string $startDateDisplayText; //we want to control how the date is displayed to the user
    public string $actionDisplayText; //we want to control the button label from BE

    public function transform($model): JsonResponse
    {
        $response = new LiveTriviaStatusResponse;
        $response->id = $model->id;
        $response->categoryId = $model->category_id;
        $response->modeId = $model->game_mode_id;
        $response->typeId = $model->game_type_id;
        $response->title = $model->name;
        $response->prize = $model->grand_price;
        $response->duration = $model->game_duration;
        $response->questionsCount = $model->question_count;
        $response->pointsRequired = $model->point_eligibility;
        $response->pointsAcquiredBeforeStart = $this->getPointsAcquiredBeforeStart($model);

        $response->startAt = $this->toNigeriaTimeZoneFromUtc($model->start_time);
        $response->startAtUtc = $this->toTimestamp($model->start_time);
        $response->status = $this->getStatus($model);
        $response->playerStatus = $this->getPlayerStatus($model, $response->pointsAcquiredBeforeStart);
        $response->prizeDisplayText = $this->getPrizeDisplayText($response->prize);
        $response->statusDisplayText = $this->getStatusDisplayText($response->status);
        $response->startDateDisplayText = $this->getStartDateDisplayText($response->startAt);
        $response->actionDisplayText = $this->getActionDisplayText($response->playerStatus, $response->status);
        return response()->json($response);
    }

    private function getPlayerStatus($model, $pointsAcquiredBefore): LiveTriviaPlayerStatus
    {
        $user = auth()->user();

        if ($user->hasPlayedTrivia($model->id)) {
            return LiveTriviaPlayerStatus::Played;
        }

        if ($pointsAcquiredBefore < $model->point_eligibility) {
            return LiveTriviaPlayerStatus::LowPoints;
        }

        return LiveTriviaPlayerStatus::CanPlay;
    }

    private function getStatus($model): LiveTriviaStatus
    {
        if (!$model->is_published) {
            return "";
        }

        $start = Carbon::parse($model->start_time);
        $end =  Carbon::parse($model->end_time);

        if ($start > now()) {
            return LiveTriviaStatus::Waiting;
        } else if ($end > now()) {
            return LiveTriviaStatus::Ongoing;
        } else if ($end->addHours(config('trivia.live_trivia.display_shelf_life')) >  now()) {
            return LiveTriviaStatus::Closed;
        } else {
            return LiveTriviaStatus::Expired;
        }
    }

    private function getActionDisplayText(LiveTriviaPlayerStatus $playerStatus, LiveTriviaStatus $status)
    {
        $result = "";

        if ($status == LiveTriviaStatus::Waiting) {
            $result = "";
        } else if ($status == LiveTriviaStatus::Ongoing) {
            if ($playerStatus == LiveTriviaPlayerStatus::LowPoints) {
                $result = "Play now";
            } else if ($playerStatus == LiveTriviaPlayerStatus::Played) {
                $result = "Leaderboard";
            } else if ($playerStatus == LiveTriviaPlayerStatus::CanPlay) {
                $result = "Play now";
            }
        }

        return $result;
    }

    private function getStartDateDisplayText($start)
    {
        return "Play " . $this->toNigeriaTimeZoneFromUtc($start)->calendar();
    }

    private function getStatusDisplayText(LiveTriviaStatus $status)
    {
        return $status->value;
    }

    private function getPrizeDisplayText($prize)
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::DECIMAL);
        return "₦" . $formatter->format($prize);
    }

    private function getPointsAcquiredBeforeStart($model)
    {
        return auth()->user()
            ->userPoints()
            ->addedBetween(
                Carbon::parse($model->start_time)->startOfDay(),
                Carbon::parse($model->end_time)
            )
            ->sum('value');
    }
}
