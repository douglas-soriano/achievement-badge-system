<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Events\LessonWatched;
use App\Services\AchievementService;

class LessonWatchedListener
{
    private AchievementService $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function handle(LessonWatched $event)
    {
        $user = $event->user;

        // Check for achievement unlocks based on watched lessons
        $this->achievementService->checkForLessonWatchAchievements($user, $event->lesson);
    }
}
