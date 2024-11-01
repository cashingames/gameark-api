<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AchievementBadge extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'milestone_type', 'milestone', 'milestone_count', 'reward_type', 'reward','description', 'medal', 'quality_image'];

    public function userAchievementBadge()
    {
        return $this->belongsToMany(AchievementBadge::class, 'user_achievement_badges');
    }
}
