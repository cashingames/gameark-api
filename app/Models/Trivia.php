<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trivia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'trivias';

    protected $fillable = ['name', 'category_id', 'game_type_id', 'game_mode_id', 'grand_price', 'point_eligibility', 'start_time', 'end_time', 'is_published'];
    protected $appends = ['is_active', 'has_played'];
    protected $casts = ['is_published' => 'boolean'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function triviaQuestions()
    {
        return $this->hasMany(TriviaQuestion::class);
    }

    public function gameSessions()
    {
        return $this->hasMany(GameSession::class);
    }

    public function getIsActiveAttribute()
    {
        if ($this->is_published) {
            if (($this->start_time <= Carbon::now('Africa/Lagos')) &&
                ($this->end_time > Carbon::now('Africa/Lagos'))
            ) {
                return true;
            }
            return false;
        }
        return false;
    }

    public function getHasPlayedAttribute()
    {
        $gameSession = $this->gameSessions()->where('user_id', auth()->user()->id)->first();

        if ($gameSession === null) {
            return false;
        }

        return true;
    }

    /**
     * Scope a query to only include the most recent upcoming live trivia.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNextUpcoming(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->where('start_time', '>=', Carbon::now('Africa/Lagos'))
            ->orderBy('start_time', 'ASC');
    }
}
