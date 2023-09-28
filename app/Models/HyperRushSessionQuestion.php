<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HyperRushSessionQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['question_id', 'option_id', 'hyper_rush_game_sessions_id', 'created_at','updated_at'];
}
