<?php

namespace App\Http\Controllers;

use App\Models\UserLevel;

class GetUserLevelController extends BaseController
{
    public function __invoke()
    {
        $userLevel = UserLevel::where('user_id', auth()->user()->id)->first();
        $userCurrentLevel=[];
        if($userLevel){
            $userCurrentLevel = [
                'level' => $userLevel->user_level,
            ];
        }
        return $this->sendResponse($userCurrentLevel, 'User level gotten successfully');
    }
}
