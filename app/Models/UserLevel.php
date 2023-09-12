<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLevel extends Model
{
    use HasFactory;
    protected $fillable = ["user_id", "user_level"];

    public function user(){
        return $this->belongsTo(User::class);
    } 
}
