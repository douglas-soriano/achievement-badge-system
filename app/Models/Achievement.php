<?php

namespace App\Models;

use App\Models\AchievementRequirement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\hasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Achievement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Requirements to unlock this achievements
     */
    public function requirements(): hasMany
    {
        return $this->hasMany(AchievementRequirement::class);
    }

}