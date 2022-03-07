<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Models\UserPlan;
use App\Models\User;
use App\Models\Plan;

class GiveDailyBonusGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bonus:daily-activate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gives daily bonus games';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {  
        User::all()->map(function ($user) {
            
            $freePlan = Plan::where('is_free',true)->first();

            UserPlan::create([
                'plan_id' => $freePlan->id,
                'user_id' => $user->id,
                'used_count' => 0,
                'plan_count' => 10,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'expire_at' => Carbon::now()->endOfDay()
            ]);
        });

    }
}