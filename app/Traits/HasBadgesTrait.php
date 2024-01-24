<?php

namespace App\Traits;

use App\Events\BadgeUnlocked;
use App\Models\Badge;
use App\Models\UserBadge;
use Illuminate\Database\Eloquent\Relations\hasManyThrough;

trait HasBadgesTrait
{

    /**
     * The badges unlocked by this user.
     */
    public function badges(): hasManyThrough
    {
        return $this->hasManyThrough(Badge::class, UserBadge::class, 'user_id', 'id', 'id', 'badge_id');
    }

    /**
     * Check if the user has this badge
     **/
    public function hasBadge(Badge $badge, bool $disable_cache = false): bool
    {
        if ($disable_cache)
            $this->load('badges');
        return $this->badges && $this->badges->contains($badge);
    }

    /**
     * Unlock badge for user and fire event.
     */
    public function unlockBadge(Badge $badge): void
    {
        // Check if the user already has the badge
        if (!$this->hasBadge($badge)) {
            // Unlock badge for user
            UserBadge::create([
                'user_id' => $this->id,
                'badge_id' => $badge->id
            ]);

            // Fire BadgeUnlocked event
            event(new BadgeUnlocked($badge->name, $this));
        }
    }
}