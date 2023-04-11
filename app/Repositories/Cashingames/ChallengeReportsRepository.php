<?php

namespace App\Repositories\Cashingames;

use App\Models\ChallengeRequest;

class ChallengeReportsRepository
{

    public function getTotalChallengeSessions($startDate, $endDate)
    {
        return ChallengeRequest::where('status', '=', 'COMPLETED')->where('user_id', '!=', 1)
            ->whereBetween('created_at', [$startDate->toDateString(), $endDate->toDateString()])->count();
    }
}
