<?php

namespace App\Services;

use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\Comment;
use App\Models\Lesson;
use App\Services\BadgeService;

class AchievementService
{
    private $badgeService;

    public function __construct(BadgeService $badgeService)
    {
        $this->badgeService = $badgeService;
    }

    /* *
     *
     * */
    public function checkForLessonWatchAchievements(User $user, Lesson $lesson): void
    {
        // Fetch relevant achievements for lesson watching
        $lesson_achievements = Achievement::where('category', 'lessons')->get();

        foreach ($lesson_achievements as $achievement) {
            // Check achievement criteria based on lesson data
            if ($this->meetsLessonAchievementRequirements($achievement, $user, $lesson)) {
                // Unlock achievement for user
                $user->achievements()->attach($achievement);

                // Fire AchievementUnlocked event
                event(new AchievementUnlocked(['achievement_name' => $achievement->name, 'user' => $user]));

                // Check for potential badge unlocks
                $this->badgeService->checkForBadgeUnlocks($user);
            }
        }
    }

    /* *
     * Helper method to encapsulate achievement criteria logic
     * */
    private function meetsLessonAchievementRequirements(Achievement $achievement, User $user, Lesson $lesson): bool
    {
        // Access achievement requirements
        $requirements = $achievement->requirements;

        // Check if all requirements are met
        foreach ($requirements as $requirement) {
            switch ($requirement->type) {

                case 'total_lessons_watched':
                    if ($user->lessons()->count() < $requirement->value) {
                        return ($user->lessons()->count() < $requirement->value); // Fail early if lesson count doesn't meet requirement
                    }
                    break;

                //
                // Example for others achievements type
                /*
                case 'lesson_category':
                    if ($lesson->category->id !== $requirement->value) {
                        return false;
                    }
                    break;
                */

                default:
                    return false; // Treat unknown requirement types as not met
            }
        }

        // If all requirements passed, return true
        return true;
    }

    /* *
     *
     * */
    public function checkForCommentWriteAchievements(User $user, Comment $comment): void
    {
        // Fetch relevant achievements for comment writing
        $comment_achievements = Achievement::where('category', 'comments')->get();

        foreach ($comment_achievements as $achievement) {
            // Check achievement criteria based on comment data
            if ($this->meetsCommentAchievementRequirements($achievement, $user, $comment)) {
                // Unlock achievement for user
                $user->achievements()->attach($achievement);

                // Fire AchievementUnlocked event
                event(new AchievementUnlocked(['achievement_name' => $achievement->name, 'user' => $user]));

                // Check for potential badge unlocks
                $this->badgeService->checkForBadgeUnlocks($user);
            }
        }
    }

    /**
    * Helper method to encapsulate achievement criteria logic
    **/
    private function meetsCommentAchievementRequirements(Achievement $achievement, User $user, Comment $comment): bool
    {
        // Access achievement requirements
        $requirements = $achievement->requirements;

        // Check if all requirements are met
        foreach ($requirements as $requirement) {
            switch ($requirement->type) {

                case 'total_comments':
                    if ($user->comments()->count() < $requirement->value) {
                        return false; // Fail early if comment count doesn't meet requirement
                    }
                    break;

                // Example of others comments achievements types
                /*
                case 'comment_length':
                    if (strlen($comment->body) < $requirement->value) {
                        return false;
                    }
                    break;
                */

                default:
                    return false; // Treat unknown requirement types as not met
            }
        }

        // If all requirements are met, return true
        return true;
    }

    /* *
     *
     * */
    public function getUserAchievements(User $user): array
    {
        // Get unlocked achievements and next available ones
        $unlocked_achievements = $user->achievements;
        $next_available_achievements = $this->getNextAvailableAchievements($user);

        // Get badge information
        $current_badge = $this->badgeService->getCurrentBadge($user);
        $next_badge = $this->badgeService->getNextBadge($user);
        $remaining_to_unlock_next_badge = $this->badgeService->getRemainingToUnlockNextBadge($user);

        // Format data for response
        return [
            'unlocked_achievements' => $unlocked_achievements->pluck('name'),
            'next_available_achievements' => $next_available_achievements->map->name,
            'current_badge' => $current_badge,
            'next_badge' => $next_badge,
            'remaining_to_unlock_next_badge' => $remaining_to_unlock_next_badge,
        ];
    }

    /* *
     *
     * */
    public function getNextAvailableAchievements(User $user): Collection
    {
        // Get all possible achievements
        $all_achievements = Achievement::with('requirements')->get();

        // Filter out already unlocked achievements
        $available_achievements = $all_achievements->filter(function ($achievement) use ($user) {
            return !$user->achievements->contains($achievement);
        });

        // Sort available achievements based on requirement types and categories
        $available_achievements = $available_achievements->sortBy(function ($achievement) {
            return $achievement->requirements->first()->type;
        })->sortBy('category');

        // Group achievements by category and take the first from each group
        $grouped_achievements = $available_achievements->groupBy('category');
        $next_available_achievements = $grouped_achievements->map(function ($group) {
            return $group->first();
        });

        return $next_available_achievements->flatten();
    }

}