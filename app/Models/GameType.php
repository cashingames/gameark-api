<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameType extends Model
{
    use HasFactory;

    protected $fillable = ['name','description', 'instruction','category_id'];

    public function category(){
        return $this->belongsTo(Category::class);
    }

}