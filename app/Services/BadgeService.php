<?php

namespace App\Services;

use App\Events\BadgeUnlocked;
use App\Models\Badge;
use App\Models\User;
use App\Models\UserBadge;

class BadgeService
{
    /**
    * Retrieves the highest badge the user has already earned.
    **/
    public function getCurrentBadge(User $user): ?Badge
    {
        $current_user_achievements_count = $user->achievements ? $user->achievements->count() : 0;
        return Badge::where('minimum_achievements_count', '<=', $current_user_achievements_count)->orderBy('minimum_achievements_count', 'DESC')->first();
    }

    /**
    * Retrieves the next badge the user can earn based on their achievement count.
    **/
    public function getNextBadge(User $user): ?Badge
    {
        $current_user_achievements_count = $user->achievements ? $user->achievements->count() : 0;
        return Badge::where('minimum_achievements_count', '>', $current_user_achievements_count)->orderBy('minimum_achievements_count', 'ASC')->first();
    }

    /**
    * Calculates the number of achievements remaining to unlock the next badge.
    **/
    public function getRemainingToUnlockNextBadge(User $user): int
    {
        $current_user_achievements_count = $user->achievements ? $user->achievements->count() : 0;
        $next_badge = $this->getNextBadge($user);
        return $next_badge ? $next_badge->minimum_achievements_count - $current_user_achievements_count : 0;
    }

    /**
    * Checks if the user has unlocked any new badges based on their current achievement count.
    **/
    public function checkForBadgeUnlocks(User $user): void
    {
        // Get the user's current achievement count
        $current_user_achievements_count = $user->achievements ? $user->achievements->count() : 0;

        // Find the first badge with minimum_achievements_count greater than the current count
        $next_badge = Badge::where('minimum_achievements_count', '<=', $current_user_achievements_count)->orderBy('minimum_achievements_count', 'DESC')->first();

        // If a qualifying badge is found and the user hasn't already earned it, unlock it
        if ($next_badge && !$user->hasBadge($next_badge, $disable_cache=true)) {
            // Unlock badge for user
            $user->unlockBadge($next_badge);
        }
    }
}