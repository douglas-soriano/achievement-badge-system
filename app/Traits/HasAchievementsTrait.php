<?php

namespace App\Traits;

use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\UserAchievement;
use Illuminate\Database\Eloquent\Relations\hasManyThrough;

trait HasAchievementsTrait
{

    /**
     * The achievements unlocked by this user.
     */
    public function achievements(): hasManyThrough
    {
        return $this->hasManyThrough(Achievement::class, UserAchievement::class, 'user_id', 'id', 'id', 'achievement_id');
    }

    /**
     * Check if the user has this achievement
     **/
    public function hasAchievement(Achievement $achievement, bool $disable_cache = false): bool
    {
        if ($disable_cache)
            $this->load('achievements');
        return $this->achievements && $this->achievements->contains($achievement);
    }

    /**
     * Unlock achievement for user and fire event.
     */
    public function unlockAchievement(Achievement $achievement): void
    {
        // Check if the user already has the achievement
        if (!$this->hasAchievement($achievement, $disable_cache=true)) {
            // Unlock achievement for user
            UserAchievement::create([
                'user_id' => $this->id,
                'achievement_id' => $achievement->id
            ]);

            // Fire AchievementUnlocked event
            event(new AchievementUnlocked($achievement->name, $this));
        }
    }
}