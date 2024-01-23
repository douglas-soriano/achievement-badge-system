<?php

namespace App\Services;

use App\Events\BadgeUnlocked;
use App\Models\Badge;

class BadgeService
{
    /**
    *
    **/
    public function getCurrentBadge(User $user): ?Badge
    {
        $current_achievement_count = $user->achievements->count();
        return Badge::where('minimum_achievements_count', '<=', $current_achievement_count)->orderBy('minimum_achievements_count', 'desc')->first();
    }

    /**
    *
    **/
    public function getNextBadge(User $user): ?Badge
    {
        $current_achievement_count = $user->achievements->count();
        return Badge::where('minimum_achievements_count', '>', $current_achievement_count)->orderBy('minimum_achievements_count', 'asc')->first();
    }

    /**
    *
    **/
    public function getRemainingToUnlockNextBadge(User $user): int
    {
        $next_badge = $this->getNextBadge($user);
        return $next_badge ? $next_badge->minimum_achievements_count - $user->achievements->count() : 0;
    }

    /**
    *
    **/
    public function checkForBadgeUnlocks(User $user): void
    {
        // Get the user's current badge count
        $current_achievement_count = $user->achievements()->count();

        // Find the first badge with minimum_achievements_count greater than the current count
        $next_badge = Badge::where('minimum_achievements_count', '>', $current_achievement_count)->orderBy('minimum_achievements_count', 'asc')->first();

        // If a qualifying badge is found and the user hasn't already earned it, unlock it
        if ($next_badge && !$user->badges()->contains($next_badge)) {
            $user->badges()->attach($next_badge);

            // Fire BadgeUnlocked event
            event(new BadgeUnlocked(['badge_name' => $next_badge->name, 'user' => $user]));
        }
    }
}