<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\ResponseHelpers\LiveTriviaStatusResponse;


class GetRecentLiveTriviaController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {   
    
        $recentLiveTrivia = DB::table('trivias')->where('is_published', true)->latest()->limit(10)->get();

        $response = [];
       
        foreach($recentLiveTrivia as $liveTrivia){
            $response[]= (new LiveTriviaStatusResponse())->transformAndReturnObject($liveTrivia);
        }
        return $response;
    }

}
