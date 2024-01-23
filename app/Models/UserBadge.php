<?php

namespace App\Models;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserBadge extends Model
{
    protected $fillable = [
        'user_id',
        'badge_id',
    ];

    /**
     * The user that has the achievement.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The badge unlocked by the user.
     */
    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }
}