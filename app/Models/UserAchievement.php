<?php

namespace App\Models;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserAchievement extends Model
{
    protected $fillable = [
        'user_id',
        'achievement_id',
    ];


    /**
     * The user that has the achievement.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The achievement unlocked by the user.
     */
    public function achievement()
    {
        return $this->belongsTo(Achievement::class);
    }
}