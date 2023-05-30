<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardBenefit extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_hours',
        'reward_type',
        'reward_count'
    ];

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }
}