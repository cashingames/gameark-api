<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HyperRushGameSession extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','start_time','end_time','session_token','state','correct_count', 'high_score', 'wrong_count','total_count', 'created_at','updated_at'];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
