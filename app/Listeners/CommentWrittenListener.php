<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Events\CommentWritten;
use App\Services\AchievementService;

class CommentWrittenListener
{
    private AchievementService $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function handle(CommentWritten $event)
    {
        $user = $event->user;

        // Check for achievement unlocks based on written comments
        $this->achievementService->checkForCommentWriteAchievements($user, $event->comment);
    }
}
