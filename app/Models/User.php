<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\DB;
use stdClass;
use Illuminate\Support\Carbon;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'phone_number',
        'password',
        'otp_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'achievement', 'rank', 'played_games_count',
        'challenges_played', 'win_rate', 'active_plan'
    ];
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function transactions()
    {
        return $this->hasManyThrough(WalletTransaction::class, Wallet::class);
    }

    public function userPlan()
    {
        return $this->hasOne(UserPlan::class);
    }

    public function boosts()
    {
        return $this->hasMany(UserBoost::class);
    }

    public function achievements()
    {
        return $this->hasMany(Achievement::class);
    }

    public function plan()
    {
        return $this->hasOne(Plan::class);
    }

    public function categories()
    {
        return $this->belongsToMany(User::class, 'game_sessions')->withPivot('points_gained', 'user_id');
    }

    public function gameSessions()
    {
        return $this->hasMany(GameSession::class);
    }

    public function points()
    {
        $pointsAdded = UserPoint::where('user_id', $this->id)
            ->where('point_flow_type', 'POINTS_ADDED')
            ->sum('value');
        $pointsSubtracted = UserPoint::where('user_id', $this->id)
            ->where('point_flow_type', 'POINTS_SUBTRACTED')
            ->sum('value');
        return $pointsAdded -  $pointsSubtracted;
    }

    public function getAchievementAttribute()
    {
        $latestAchievement = DB::table('user_achievements')
            ->where('user_id', $this->id)->latest()->first();

        if ($latestAchievement === null) {
            return " ";
        }
        $achievement = Achievement::where('id', $latestAchievement->achievement_id)->first();

        return ($achievement->title);
    }


    public function getRankAttribute()
    {
        $results = DB::select(
            "select SUM(value) as score, user_id from user_points WHERE 
            point_flow_type = 'POINTS_ADDED'
            group by user_id
            order by score desc
            limit 100"
        );

        $userIndex = -1;

        if (count($results) > 0) {
            $userIndex = collect($results)->search(function ($user) {
                return $user->user_id == $this->id;
            });
        }

        if ($userIndex === false || $userIndex === -1) {
            return 786;
        }

        return $userIndex + 1;
    }

    public function getPlayedGamesCountAttribute()
    {
        return GameSession::where('user_id', $this->id)->count();
    }

    public function hasActivePlan()
    {   
        $isPlanActive = $this->userPlan->is_active;
        if (!$isPlanActive) {
            $lastGamePlayed = GameSession::where('user_id', $this->id)->latest()->first();

            if ($lastGamePlayed === null || $lastGamePlayed->created_at <= Carbon::now()->subDay()) {
                //subscribe user to free plan
                $this->userPlan->update(['plan_id' => 1, 'is_active' => true]);
                return true;
            }
            return false;
        }
        $recentGamesCount = $this->gameSessions->where('created_at', '>=', Carbon::now()->subDay())->count();
        $plan = Plan::where('id', $this->userPlan->plan_id)->first();

        if ($recentGamesCount >= $plan->game_count) {
            $this->userPlan->update(['is_active' => false]);
            return false;
        }
        return true;
    }

    public function getActivePlanAttribute()
    {   
        //for already signed up users before plan was introduced subscribe to free plan
        $subscribedPlan = UserPlan::where('user_id', $this->id)->first();
        if($subscribedPlan === null){
            DB::table('user_plans')->insert([
                'user_id' => $this->id,
                'plan_id' => 1,
                'is_active'=> true
            ]);
        }

        $plan = Plan::where('id', $subscribedPlan->plan_id)->first();

        return $plan->name;
    }

    public function getChallengesPlayedAttribute()
    {
        return GameSession::where('user_id', $this->id)->where('game_mode_id', 2)->count();
    }

    public function getWinRateAttribute()
    {
        $gameWins = GameSession::where('correct_count', '>=', 5)->count();
        return ($gameWins / 100);
    }

    public function friends()
    {

        return User::where('id', '!=', $this->id)->get()->map(function ($friend) {
            $data = new stdClass;
            $data->id = $friend->id;
            $data->fullName = $friend->profile->full_name;
            $data->username = $friend->username;
            $data->avatar = $friend->profile->avatar;
            return $data;
        });
    }


    public function pointTransactions()
    {
        return $this->hasMany(UserPoint::class);
    }

    public function getUserPointTransactions()
    {
        return $this->pointTransactions()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function userTransactions()
    {
        return $this->transactions()
            ->select('transaction_type as type', 'amount', 'description', 'wallet_transactions.created_at as transactionDate')
            ->orderBy('transactionDate', 'desc')
            ->limit(10)
            ->get();
    }

    public function recentGames()
    {
        return $this->gameSessions()->latest()
            ->select('category_id')
            ->groupBy('category_id')->limit(3)->get()
            ->map(function ($x) {
                return $x->category()->select('id', 'name', 'description', 'background_color as bgColor', 'icon as icon')->first();
            });
    }

    public function userAchievements()
    {
        return DB::table('user_achievements')->where('user_id', $this->id)
            ->join('achievements', function ($join) {
                $join->on('achievements.id', '=', 'user_achievements.achievement_id');
            })->select('achievements.id', 'title', 'medal as logoUrl')->get();
    }

    public function userBoosts()
    {
        return DB::table('user_boosts')
            ->where('user_id', $this->id)
            ->join('boosts', function ($join) {
                $join->on('boosts.id', '=', 'user_boosts.boost_id');
            })->select('boosts.id', 'name', 'user_boosts.boost_count as count')
            ->where('user_boosts.boost_count', '>', 0)->get();
    }
}
