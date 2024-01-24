<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\hasManyThrough;

class Badge extends Model
{

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'image',
        'minimum_achievements_count',
    ];

    /**
     * Users that has this badge
     */
    public function users(): hasManyThrough
    {
        return $this->hasManyThrough(User::class, UserBadge::class, 'badge_id', 'user_id');
    }
}